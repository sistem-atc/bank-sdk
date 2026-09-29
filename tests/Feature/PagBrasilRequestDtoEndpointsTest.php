<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\PagBrasil\DTO\Request\Checkout\PaymentLink;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Address;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Product;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\RefundPixKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\CardOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\PixOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\Refund;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\Cancellation;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\RecurrenceItem;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ShippingChange;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ShippingsUpdate;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\SubscriptionFilter;
use SistemAtc\Banks\PagBrasil\DTO\Request\Payout\PayeeData;
use SistemAtc\Banks\PagBrasil\Enums\CancellationOption;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\PagBrasil\Enums\PixKeyType;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

function pbDtoIntegration(): FakePagBrasilIntegration
{
    return new FakePagBrasilIntegration(signatureKey: null);
}

function pbDtoPix(): PixOrder
{
    return new PixOrder(
        'PED-9', 'Whey 900g', [new Product('WHEY-900', 129.9, 1)],
        new Customer('José da Silva', '529.982.247-25', 'jose@example.com', '11999990000'),
        new Address('Av. Paulista, 100', '01311-100', 'São Paulo', 'SP'),
        129.9, expirationMinutes: 30,
    );
}

it('pedido Pix por DTO vira o form da API clássica', function () {
    Http::fake(['*/api/order/add' => Http::response('<request><order>PED-9</order><order_status>WP</order_status><pix_code>000201</pix_code></request>')]);

    $order = Bank::PagBrasil->pedidos(pbDtoIntegration())->pix(pbDtoPix());

    expect($order->pixCode)->toBe('000201');
    Http::assertSent(fn (Request $r) => $r->isForm()
        && $r['secret'] === 'SECRET' && $r['payment_method'] === 'X' && $r['order'] === 'PED-9'
        && $r['amount_brl'] === '129.90' && $r['pix_expiration'] === '30'
        && $r['customer_taxid'] === '52998224725' && $r['address_zip'] === '01311100'
        && $r['products'] === '[{"sku":"WHEY-900","amount":129.9,"quantity":1,"discount":0}]');
});

it('cartão por DTO: cc_save vira "0" no form', function () {
    Http::fake(['*/api/order/add' => Http::response('<request><order>C1</order><order_status>PC</order_status></request>')]);

    $pix = pbDtoPix();
    Bank::PagBrasil->pedidos(pbDtoIntegration())->cartao(new CardOrder(
        'C1', 'Whey', $pix->products, $pix->customer, $pix->address, 129.9, previousOrder: 'C0',
    ));

    Http::assertSent(fn (Request $r) => $r['payment_method'] === 'C' && $r['cc_save'] === '0'
        && $r['cc_auth'] === '0' && $r['cc_prevsaved'] === 'C0' && $r['cc_installments'] === '1');
});

it('estorno por DTO e pela forma curta com destino tipado', function () {
    Http::fake(['*/api/order/refund' => Http::response('Refund request received')]);

    $orders = Bank::PagBrasil->pedidos(pbDtoIntegration());
    $orders->estornar(new Refund('P1', 10, new RefundPixKey(PixKeyType::Email, 'a@b.com')));
    $orders->estornar('P2', '5.00', new RefundPixKey(PixKeyType::Phone, '11999990000'));

    Http::assertSent(fn (Request $r) => $r['order'] === 'P1' && $r['amount_refunded'] === '10.00'
        && $r['customer_pix_key_type'] === 'email' && $r['suspicious'] === '0');
    Http::assertSent(fn (Request $r) => $r['order'] === 'P2' && $r['customer_pix_key_type'] === 'phone');
    expect(fn () => $orders->estornar('P3'))->toThrow(InvalidArgumentException::class);
});

it('link de pagamento e favorecido por DTO', function () {
    Http::fake([
        '*/api/checkout/add' => Http::response('<request><url_payment>https://pay/x</url_payment></request>'),
        '*/api/payout/' => Http::response(['action' => 'addpayee', 'success' => true]),
    ]);

    expect(Bank::PagBrasil->linkPagamento(pbDtoIntegration())->criar(new PaymentLink('L1', 50, paymentOptions: [PaymentMethod::Pix]))->urlPayment)
        ->toBe('https://pay/x');

    expect(fn () => Bank::PagBrasil->payout(pbDtoIntegration())->cadastrarFavorecido(new PayeeData('91051605962', name: 'José')))
        ->toThrow(InvalidArgumentException::class, 'sem bank');

    Bank::PagBrasil->payout(pbDtoIntegration())->atualizarFavorecido(new PayeeData('91051605962', email: 'novo@example.com'));

    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/api/checkout/add') && $r['payment_option'] === 'X' && $r['amount_brl'] === '50.00');
    Http::assertSent(fn (Request $r) => ($r['action'] ?? null) === 'updatepayee' && $r['payee_email'] === 'novo@example.com' && ! isset($r['payee_name']));
});

it('PagStream por DTO: filtro na query, corpo JSON tipado', function () {
    Http::fake([
        '*/subscriptions?*' => Http::response(['data' => [], 'page' => 1, 'per_page' => 24, 'total' => 0, 'total_pages' => 0]),
        '*/subscriptions/P1/cancel' => Http::response(['subscription_number' => 'P1', 'status' => 'canceled']),
        '*/recurrences/next/items' => Http::response(['subscription_number' => 'P1', 'recurrence_number' => 4]),
        '*/subscriptions/P1/shippings' => Http::response(['first_buy_shippings' => [], 'renewal_shippings' => []]),
    ]);

    $stream = Bank::PagBrasil->pagStream(pbDtoIntegration());
    $stream->assinaturas()->listar(new SubscriptionFilter(email: 'maria@example.com'));
    $stream->assinaturas()->cancelar('P1', new Cancellation(CancellationOption::NotSatisfied));
    $stream->recorrencias()->adicionarItem('P1', 'next', new RecurrenceItem('SKU-1', 2, 24.95));
    $stream->envios()->alterar('P1', new ShippingsUpdate(renewal: [0 => [ShippingChange::cancelar(10)]]));

    Http::assertSent(fn (Request $r) => str_contains($r->url(), '/subscriptions?email=maria%40example.com'));
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/cancel') && $r->body() === '{"cancellation_option":"not_satisfied"}');
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/items') && $r->body() === '{"product_sku":"SKU-1","quantity":2,"price":"24.95"}');
    // Recorrência 0: continua OBJETO no JSON (seria lista sem o cast).
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/shippings')
        && $r->body() === '{"renewal_shippings":{"0":[{"status":"canceled","shipping_id":10}]}}');
});
