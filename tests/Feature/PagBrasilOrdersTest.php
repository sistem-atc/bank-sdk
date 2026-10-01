<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Exceptions\PagBrasilRequestException;
use SistemAtc\Banks\Exceptions\PagBrasilSignatureException;
use SistemAtc\Banks\PagBrasil\DTO\Response\Orders\Order;
use SistemAtc\Banks\PagBrasil\Enums\CardBrand;
use SistemAtc\Banks\PagBrasil\Enums\CardErrorCode;
use SistemAtc\Banks\PagBrasil\Enums\OrderStatus;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

function pbXml(string $fixture): string
{
    return (string) file_get_contents(__DIR__.'/../Fixtures/PagBrasil/'.$fixture);
}

function pbOrders(?FakePagBrasilIntegration $integration = null)
{
    return Bank::PagBrasil->pedidos($integration ?? new FakePagBrasilIntegration());
}

it('cria pedido de cartão em form-urlencoded, credenciais no corpo, e hidrata o XML Latin-1', function () {
    Http::fake(['sandbox.pagbrasil.com/api/order/add' => Http::response(pbXml('card-paid.xml'), 200, ['Content-Type' => 'text/xml'])]);

    $order = pbOrders()->cartao([
        'order' => '1234567890',
        'product_name' => 'Product Test (1 license)',
        'products' => [['sku' => 'productSKU', 'amount' => 39.5, 'quantity' => 1, 'discount' => 0]],
        'customer_name' => 'José da Silva',
        'amount_brl' => 39.5,
        'cc_installments' => 1,
    ]);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->status())->toBe(OrderStatus::Completed)
        ->and($order->isPaid())->toBeTrue()
        ->and($order->customerName)->toBe('José da Silva')
        ->and($order->addressCity)->toBe('São Paulo')
        ->and($order->brand())->toBe(CardBrand::Visa)
        ->and($order->amountPaid)->toBe('39.50')
        ->and($order->date('payment_date')?->format('Y-m-d'))->toBe('2010-10-12');

    Http::assertSent(function (Request $r) {
        return $r->url() === 'https://sandbox.pagbrasil.com/api/order/add'
            && $r->isForm()
            && $r['secret'] === 'SECRET'
            && $r['pbtoken'] === 'PBTOKEN'
            && $r['payment_method'] === 'C'
            && $r['cc_save'] === '0'
            && $r['cc_auth'] === '0'
            && $r['amount_brl'] === '39.50'
            && $r['products'] === '[{"sku":"productSKU","amount":39.5,"quantity":1,"discount":0}]';
    });
});

it('não deixa o chamador sobrescrever secret/pbtoken pelos dados', function () {
    Http::fake(['*/api/order/add' => Http::response(pbXml('boleto-paid.xml'))]);

    pbOrders()->boleto(['order' => '1', 'secret' => 'X', 'pbtoken' => 'Y', 'bol_expiration' => 3]);

    Http::assertSent(fn (Request $r) => $r['secret'] === 'SECRET' && $r['pbtoken'] === 'PBTOKEN'
        && $r['payment_method'] === 'B' && $r['bol_expiration'] === '3');
});

it('traz o motivo da recusa do cartão', function () {
    Http::fake(['*/api/order/add' => Http::response(pbXml('card-declined.xml'))]);

    $order = pbOrders()->cartao(['order' => '1234567890']);

    expect($order->status())->toBe(OrderStatus::Failed)
        ->and($order->cardError())->toBe(CardErrorCode::DeclinedByIssuer)
        ->and($order->isPaid())->toBeFalse();
});

it('transforma o erro em texto puro (HTTP 200) em exceção', function () {
    Http::fake(['*/api/order/add' => Http::response('Duplicated order.', 200)]);

    expect(fn () => pbOrders()->pix(['order' => '1']))
        ->toThrow(PagBrasilRequestException::class, '[pagbrasil] Duplicated order.');
});

it('recusa resposta com assinatura adulterada', function () {
    $tampered = str_replace('<amount_paid>39.50</amount_paid>', '<amount_paid>9.50</amount_paid>', pbXml('pix-paid.xml'));
    Http::fake(['*/api/order/get' => Http::response($tampered)]);

    expect(fn () => pbOrders()->consultar('1234567890'))->toThrow(PagBrasilSignatureException::class);
});

it('não confere assinatura quando a integração não tem signature key', function () {
    $tampered = str_replace('<amount_paid>39.50</amount_paid>', '<amount_paid>9.50</amount_paid>', pbXml('pix-paid.xml'));
    Http::fake(['*/api/order/get' => Http::response($tampered)]);

    $order = pbOrders(new FakePagBrasilIntegration(signatureKey: null))->consultar('1234567890');

    expect($order?->amountPaid)->toBe('9.50')->and($order?->pixCode)->toStartWith('9999');
});

it('consulta devolve null quando o pedido não existe', function () {
    Http::fake(['*/api/order/get' => Http::response(pbXml('order-not-found.xml'))]);

    expect(pbOrders()->consultar('nao-existe'))->toBeNull();
});

it('consulta repete em 5xx (leitura é idempotente)', function () {
    Http::fakeSequence('*/api/order/get')
        ->push('erro', 503, ['Retry-After' => '0'])
        ->push(pbXml('boleto-paid.xml'));

    expect(pbOrders()->consultar('1234567890')?->urlBoleto)->toBe('https://pagbrasil.com/b?b3b4cj7rf');
    Http::assertSentCount(2);
});

it('criação NÃO repete em 5xx (evita cobrança duplicada)', function () {
    Http::fake(['*/api/order/add' => Http::response('erro', 503, ['Retry-After' => '0'])]);

    expect(fn () => pbOrders()->pix(['order' => '1']))->toThrow(PagBrasilRequestException::class);
    Http::assertSentCount(1);
});

it('captura pré-autorização mandando só o necessário com cc_auth=2', function () {
    Http::fake(['*/api/order/add' => Http::response(pbXml('card-paid.xml'))]);

    pbOrders()->capturar('test3', '75.00');

    Http::assertSent(fn (Request $r) => $r['cc_auth'] === '2' && $r['amount_brl'] === '75.00'
        && $r['order'] === 'test3' && $r['payment_method'] === 'C' && ! isset($r['cc_save']));
});

it('estorna e aceita só a confirmação textual', function () {
    Http::fakeSequence('*/api/order/refund')
        ->push('Refund request received')
        ->push('Refund must be by bank transfer');

    expect(pbOrders()->estornar('1234567890', 39.5))->toBe('Refund request received');
    expect(fn () => pbOrders()->estornar('1234567890', 39.5))
        ->toThrow(PagBrasilRequestException::class, 'Refund must be by bank transfer');

    Http::assertSent(fn (Request $r) => $r['amount_refunded'] === '39.50');
});

it('estorno por chave Pix leva os dados do cliente', function () {
    Http::fake(['*/api/order/refund' => Http::response('Refund request received')]);

    pbOrders()->estornar('1', '10.00', ['customer_pix_key_type' => \SistemAtc\Banks\PagBrasil\Enums\PixKeyType::Email, 'customer_pix_key' => 'a@b.com']);

    Http::assertSent(fn (Request $r) => $r['customer_pix_key_type'] === 'email' && $r['customer_pix_key'] === 'a@b.com');
});

it('prorroga boleto (URL em texto) e Pix (XML)', function () {
    Http::fakeSequence('*/api/order/extend')
        ->push('https://pagbrasil.com/b?novo')
        ->push(pbXml('pix-paid.xml'))
        ->push('Order not found.');

    expect(pbOrders()->prorrogarBoleto('1', 5))->toBe('https://pagbrasil.com/b?novo')
        ->and(pbOrders()->prorrogarPix('1', 180)->pixImage)->toBe('https://pagbrasil.com/x/img?i174bwzjqc');
    expect(fn () => pbOrders()->prorrogarBoleto('1', 5))->toThrow(PagBrasilRequestException::class, 'Order not found.');

    Http::assertSent(fn (Request $r) => ($r['extend_days'] ?? null) === '5');
    Http::assertSent(fn (Request $r) => ($r['extend_minutes'] ?? null) === '180');
});

it('cancela, apaga cartão salvo e gera o 1-Click Pix', function () {
    Http::fake([
        '*/api/order/cancel' => Http::response(str_replace('<order_status>PC</order_status>', '<order_status>PR</order_status>', pbXml('boleto-paid.xml'))),
        '*/api/order/creditcard/delete' => Http::response('Credit card information successfully deleted'),
        '*/api/pix/1click' => Http::response(['url_payment' => 'https://pay.example/1click']),
    ]);

    $integration = new FakePagBrasilIntegration(signatureKey: null);

    expect(pbOrders($integration)->cancelar('1')->status())->toBe(OrderStatus::Rejected)
        ->and(pbOrders($integration)->excluirCartaoSalvo('123456', '52998224725'))->toBe('Credit card information successfully deleted')
        ->and(pbOrders($integration)->pixUmClique('1')->urlPayment)->toBe('https://pay.example/1click');
});

it('produção devolve "não encontrado" com HTTP 412 — consulta dá null, não erro', function () {
    // Visto em connect.pagbrasil.com (01/10/2026): pedido inexistente vem com
    // status 412 e o MESMO corpo que a doc documenta com 200.
    Http::fake(['*/api/order/get' => Http::response("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<request>\n</request>", 412)]);

    expect(pbOrders()->consultar('142661'))->toBeNull();
});

it('412 com estrutura de pedido devolve o pedido; 412 em texto continua erro (credencial recusada)', function () {
    Http::fakeSequence('*/api/order/get')
        ->push(pbXml('boleto-paid.xml'), 412)
        ->push('Invalid access.', 412);

    expect(pbOrders()->consultar('1234567890')?->urlBoleto)->toBe('https://pagbrasil.com/b?b3b4cj7rf');
    expect(fn () => pbOrders()->consultar('1234567890'))
        ->toThrow(PagBrasilRequestException::class, 'Invalid access.');
});
