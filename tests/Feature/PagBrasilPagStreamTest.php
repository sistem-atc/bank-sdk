<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Exceptions\PagBrasilRequestException;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\Subscription;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

function pbStream()
{
    return Bank::PagBrasil->pagStream(new FakePagBrasilIntegration(signatureKey: null));
}

/** @return array<string, mixed> */
function pbSubscription(array $override = []): array
{
    return $override + [
        'subscription_number' => 'P17648804286',
        'status' => 'active',
        'amount' => '149.90',
        'products' => [['sku' => 'CAFE-500G', 'name' => 'Cafe Especial 500g', 'image_link' => null, 'price' => '129.90', 'discount' => '0.00']],
        'shipping_info' => ['code' => 'FRETE-SEDEX', 'name' => 'Frete Sedex', 'price' => '20.00', 'discount' => '0.00'],
        'cancellation_date' => null,
        'next_billing_date' => '2026-09-27',
        'billing_cycle' => ['code' => 'M', 'units' => 1, 'interval' => 'MONTH'],
        'shipping_cycle' => null,
        'renewals_processed' => 3,
        'renewals_overdue' => 0,
        'renewals_limit' => 0,
        'payment' => ['installments' => 1, 'card_token' => 'b7f1c0a2e4d5', 'pix_consent_id' => null],
        'cancellation_reason' => null,
        'canceled_by' => null,
        'billing_day' => 27,
        'allow_edit_item' => true,
        'cancellation_effective_date' => null,
        'custom_name' => null,
        'customer' => ['name' => 'Maria Oliveira', 'email' => 'maria@example.com', 'taxid' => '12345678909', 'birth_date' => '1990-04-12', 'phone' => '5551998877665'],
        'billing_address' => ['street' => 'Av. Ipiranga', 'number' => '', 'complement' => '', 'neighborhood' => '', 'city' => 'Porto Alegre', 'state' => 'RS', 'zipcode' => '90160091'],
        'shipping_address' => ['street' => 'Av. Ipiranga', 'number' => '', 'complement' => '', 'neighborhood' => '', 'city' => 'Porto Alegre', 'state' => 'RS', 'zipcode' => '90160091'],
    ];
}

/** @return array<string, mixed> */
function pbRecurrence(array $override = []): array
{
    return $override + [
        'subscription_number' => 'P17648804286', 'recurrence_number' => 4,
        'billing_cycle' => ['code' => 'M', 'units' => 1, 'interval' => 'MONTH'], 'shipping_cycle' => null,
        'renewal_date' => '2026-09-27', 'cancellation_date' => '2026-10-07', 'amount' => '174.81', 'discount' => '0.00',
        'status' => 'pending', 'locked' => false, 'skipped' => false, 'paused' => false, 'charge_attempts' => 0,
        'payment' => ['card_token' => 'b7f1c0a2e4d5', 'pix_consent_id' => null],
        'charge_history' => [['charge_number' => 1, 'attempted' => false, 'status' => 'pending', 'type' => 'credit_card', 'attempted_at' => null, 'scheduled_for' => '2026-09-27', 'charged_cards' => []]],
        'products' => [['sku' => 'SKU-1234', 'name' => 'Filtro', 'image_link' => null, 'quantity' => 2, 'price' => '24.95', 'discount' => '10.00']],
    ];
}

it('lista assinaturas do cliente com credenciais em headers e hidrata o objeto completo', function () {
    Http::fake(['sandbox.pagbrasil.com/api/v2/pagstream/subscriptions*' => Http::response([
        'data' => [pbSubscription()], 'page' => 1, 'per_page' => 24, 'total' => 1, 'total_pages' => 1,
    ])]);

    $list = pbStream()->assinaturas()->listar(['tax_id' => '12345678909', 'status' => 'active']);
    $sub = $list->data[0];

    expect($list->total)->toBe(1)
        ->and($sub)->toBeInstanceOf(Subscription::class)
        ->and($sub->statusEnum())->toBe(SubscriptionStatus::Active)
        ->and($sub->billingCycle?->code)->toBe('M')
        ->and($sub->payment?->cardToken)->toBe('b7f1c0a2e4d5')
        ->and($sub->products[0]->price)->toBe('129.90')
        ->and($sub->customer?->birthDate)->toBe('1990-04-12')
        ->and($sub->shippingAddress?->city)->toBe('Porto Alegre')
        ->and($sub->allowEditItem)->toBeTrue();

    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && str_contains($r->url(), '/api/v2/pagstream/subscriptions?tax_id=12345678909&status=active')
        && $r->hasHeader('pbtoken', 'PBTOKEN')
        && $r->hasHeader('secret', 'SECRET'));
});

it('pausa, reativa, cancela e altera ciclo/dia/pagamento nas rotas certas', function () {
    Http::fake(['*/api/v2/pagstream/subscriptions/P1/*' => Http::response(pbSubscription(['status' => 'canceled']))]);

    $subs = pbStream()->assinaturas();
    $subs->pausar('P1');
    $subs->reativar('P1');
    expect($subs->cancelar('P1', ['cancellation_option' => 'other', 'cancellation_reason' => 'Mudou', 'canceled_by' => 'ops'])->status)->toBe('canceled');
    $subs->alterarCiclo('P1', 'M_M4W');
    $subs->alterarDiaCobranca('P1', null);
    $subs->alterarPagamento('P1', ['payment_method' => 'credit_card', 'card_token' => 'tok', 'installments' => 3]);

    Http::assertSent(fn (Request $r) => $r->method() === 'POST' && str_ends_with($r->url(), '/subscriptions/P1/pause'));
    Http::assertSent(fn (Request $r) => $r->method() === 'POST' && str_ends_with($r->url(), '/subscriptions/P1/resume'));
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/cancel') && $r['canceled_by'] === 'ops');
    Http::assertSent(fn (Request $r) => $r->method() === 'PATCH' && str_ends_with($r->url(), '/billing-cycle') && $r['option_key'] === 'M_M4W');
    // billing_day null tem de ir como JSON null (remove o dia fixo), não sumir do corpo.
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/billing-day') && $r->body() === '{"billing_day":null}');
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/payment-method') && $r['installments'] === 3);
});

it('traduz o envelope de erro v2 em exceção com code/type/details', function () {
    Http::fake(['*/subscriptions/P1/resume' => Http::response(['error' => [
        'type' => 'invalid_request', 'code' => 'subscription_not_paused',
        'message' => 'Only paused subscriptions can be resumed. Current status: active.', 'details' => ['status' => 'active'],
    ]], 409)]);

    try {
        pbStream()->assinaturas()->reativar('P1');
        $this->fail('esperava exceção');
    } catch (PagBrasilRequestException $e) {
        expect($e->errorCode)->toBe('subscription_not_paused')
            ->and($e->errorType)->toBe('invalid_request')
            ->and($e->details)->toBe(['status' => 'active'])
            ->and($e->status())->toBe(409)
            ->and($e->getMessage())->toContain('subscription_not_paused');
    }
});

it('opera recorrências por número e pelo alias next', function () {
    Http::fake([
        '*/recurrences/next/items' => Http::response(pbRecurrence()),
        '*/recurrences/4/items/replace' => Http::response(pbRecurrence()),
        '*/recurrences/4/items/SKU-1234' => Http::response(pbRecurrence()),
        '*/recurrences/4/discount' => Http::response(pbRecurrence(['discount' => '15.00', 'locked' => true])),
        '*/recurrences/4/skip' => Http::response(pbRecurrence(['skipped' => true])),
        '*/recurrences/4/unskip' => Http::response(pbRecurrence()),
        '*/recurrences/next/charge' => Http::response(['subscription_number' => 'P1', 'recurrence_number' => 3, 'status' => 'queued', 'amount_brl' => '149.90', 'queued_at' => '2026-08-13T14:22:05-03:00'], 202),
        '*/recurrences/3/charge-attempts' => Http::response(['data' => [['attempt_id' => '8821', 'attempt_number' => 2, 'outcome' => 'error', 'type' => 'credit_card', 'attempted' => true, 'scheduled_for' => '2026-07-02', 'attempted_at' => '2026-07-02 03:11:07', 'order_status' => 'PF', 'error_code' => '51']]]),
        '*/recurrences/4' => Http::response(pbRecurrence(['renewal_date' => '2026-09-30'])),
    ]);

    $rec = pbStream()->recorrencias();

    $added = $rec->adicionarItem('P1', 'next', ['product_sku' => 'SKU-1234', 'quantity' => 2, 'price' => '24.95', 'discount' => '10.00']);
    expect($added->products[0]->quantity)->toBe(2)
        ->and($added->chargeHistory[0]->status)->toBe('pending')
        ->and($added->payment?->cardToken)->toBe('b7f1c0a2e4d5');

    $rec->substituirItens('P1', 4, ['CAFE-500G' => 'CAFE-1KG']);
    $rec->alterarItem('P1', 4, 'SKU-1234', ['quantity' => 3]);
    $rec->removerItem('P1', 4, 'SKU-1234');
    expect($rec->aplicarDesconto('P1', 4, '15.00')->discount)->toBe('15.00')
        ->and($rec->pular('P1', 4)->skipped)->toBeTrue()
        ->and($rec->despular('P1', 4)->skipped)->toBeFalse()
        ->and($rec->reagendar('P1', 4, new DateTimeImmutable('2026-09-30'))->renewalDate)->toBe('2026-09-30')
        ->and($rec->cobrar('P1', 'next')->status)->toBe('queued')
        ->and($rec->tentativas('P1', 3)->data[0]->errorCode)->toBe('51');

    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/items/replace') && $r->body() === '[{"old":"CAFE-500G","new":"CAFE-1KG"}]');
    Http::assertSent(fn (Request $r) => $r->method() === 'DELETE' && str_ends_with($r->url(), '/items/SKU-1234'));
    Http::assertSent(fn (Request $r) => $r->method() === 'PATCH' && str_ends_with($r->url(), '/recurrences/4')
        && $r['renewal_date'] === '2026-09-30' && $r['overwrite_cycle'] === false);
});

it('envios: lista a agenda (mapa por recorrência) e altera mandando objeto JSON', function () {
    $schedule = [
        'first_buy_shippings' => [['shipping_id' => 551, 'scheduled_for' => '2026-07-02', 'amount' => '20.00', 'status' => 'completed', 'total_amount' => '149.90', 'num_shipping' => 1, 'total_shippings' => 1, 'items' => [['sku' => 'SKU-1234', 'quantity' => 1, 'amount' => '129.90']]]],
        'renewal_shippings' => ['4' => [['shipping_id' => 902, 'recurrence_number' => 4, 'scheduled_for' => '2026-09-29', 'amount' => '20.00', 'status' => 'pending', 'total_amount' => '149.90', 'num_shipping' => 1, 'total_shippings' => 1, 'items' => []]]],
    ];
    Http::fake(['*/subscriptions/P1/shippings*' => Http::response($schedule)]);

    $envios = pbStream()->envios();
    $agenda = $envios->listar('P1', ['status' => 'pending']);

    expect($agenda->firstBuyShippings[0]->items[0]->amount)->toBe('129.90')
        ->and($agenda->renewalShippings[4][0]->shippingId)->toBe(902)
        ->and($agenda->toArray()['renewal_shippings'])->toHaveKey('4');

    $envios->alterar('P1', ['renewal_shippings' => [0 => [['shipping_id' => 902, 'status' => 'canceled']]]]);

    Http::assertSent(fn (Request $r) => $r->method() === 'PATCH'
        && str_contains($r->body(), '"renewal_shippings":{"0":[{"shipping_id":902,"status":"canceled"}]}'));
});

it('catálogo e cobranças da loja', function () {
    $product = ['sku' => 'SKU-1234', 'name' => 'Box', 'description' => null, 'image_link' => null, 'amount' => '129.90', 'status' => 'active',
        'on_customer_area' => true, 'single_purchase' => false, 'order_trigger' => false, 'display_order' => 1,
        'rules' => [['id' => '12', 'billing_cycle' => ['code' => 'M', 'units' => 1, 'interval' => 'MONTH'], 'shipping_cycle' => null, 'status' => 'active', 'limit' => 0, 'billing_day' => 10, 'cycle_turnover_day' => 0, 'is_default' => true, 'in_use_by_subscriptions' => true]]];

    Http::fake([
        '*/api/v2/pagstream/products/SKU-1234' => Http::response($product),
        '*/api/v2/pagstream/products*' => Http::response(['data' => [$product], 'page' => 1, 'per_page' => 24, 'total' => 1, 'total_pages' => 1]),
        '*/api/v2/pagstream/charges*' => Http::response(['data' => [['attempt_id' => '1', 'attempt_number' => 1, 'outcome' => 'error', 'type' => 'credit_card', 'attempted' => true, 'scheduled_for' => '2026-07-02', 'attempted_at' => '2026-07-02 03:11:07', 'subscription_code' => 'P1', 'order_number' => 'P1-3', 'order_status' => 'PF', 'error_code' => '51']], 'page' => 1, 'per_page' => 24, 'total' => 137, 'total_pages' => 6]),
    ]);

    $products = pbStream()->produtos()->listar(['status' => 'active', 'on_customer_area' => true]);
    expect($products->data[0]->rules[0]->isDefault)->toBeTrue()
        ->and($products->data[0]->rules[0]->billingCycle?->interval)->toBe('MONTH');

    expect(pbStream()->produtos()->configurar('SKU-1234', ['status' => 'active', 'rules' => [['id' => 12, 'billing_day' => 10]]])->sku)->toBe('SKU-1234');

    $charges = pbStream()->cobrancas()->listar(['status' => 'error', 'date_from' => '2026-07-01']);
    expect($charges->total)->toBe(137)->and($charges->data[0]->subscriptionCode)->toBe('P1');

    Http::assertSent(fn (Request $r) => str_contains($r->url(), 'products?status=active&on_customer_area=true'));
});

it('criação de assinatura e itens fixos vão pela API v1 (form, XML)', function () {
    Http::fake([
        '*/api/pagstream/subscription/add' => Http::response('<?xml version="1.0" encoding="ISO-8859-1"?><pagstream><subscription>P123</subscription><status>2</status><amount_brl>39.50</amount_brl><next_billing_date>2020-12-01</next_billing_date></pagstream>'),
        '*/api/pagstream/subscription/item/*' => Http::response('<?xml version="1.0" encoding="UTF-8"?><pagstream><subscription>P123</subscription><status>1</status>'
            .'<recurrences><item id="0"><order>REC_1</order><order_status>PC</order_status><products><item id="0"><sku>CAFE</sku><discount>0.00</discount></item></products></item></recurrences>'
            .'<products><item id="0"><sku>CAFE</sku><unit_price>129.90</unit_price></item></products></pagstream>'),
    ]);

    $subs = pbStream()->assinaturas();
    $created = $subs->criar(['products' => [['sku' => 'CAFE', 'amount' => 39.5, 'quantity' => 1, 'discount' => 0]], 'amount_brl' => '39.50', 'billing_cycle' => 'M', 'next_billing_date' => '2020-12-01']);

    expect($created->subscription)->toBe('P123')
        ->and($created->statusEnum())->toBe(SubscriptionStatus::AwaitingPayment);

    $legacy = $subs->adicionarItemFixo('P123', 'CAFE', ['quantity' => 1, 'amount_brl' => '129.90']);
    expect($legacy->statusEnum())->toBe(SubscriptionStatus::Active)
        ->and($legacy->recurrences[0]->order)->toBe('REC_1')
        ->and($legacy->recurrences[0]->products[0]->sku)->toBe('CAFE')
        ->and($legacy->products[0]->unitPrice)->toBe('129.90');

    $subs->removerItemFixo('P123', 'CAFE');

    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/api/pagstream/subscription/add') && $r->isForm()
        && $r['secret'] === 'SECRET' && $r['billing_cycle'] === 'M');
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/item/delete') && ! isset($r['quantity']) && $r['sku'] === 'CAFE');
});
