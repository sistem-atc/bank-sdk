<?php

declare(strict_types=1);

use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Exceptions\PagBrasilSignatureException;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\BoletosPaidNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\ConsentNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\LegacySubscriptionNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\PayeeNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\PaymentNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\PayoutNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\ProductNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\ShippingCanceledNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\SubscriptionNotification;
use SistemAtc\Banks\PagBrasil\Enums\PaymentStatus;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;
use SistemAtc\Banks\PagBrasil\Support\Signature;
use SistemAtc\Banks\PagBrasil\Webhooks\WebhookVerifier;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

function pbWebhook(?string $key = FakePagBrasilIntegration::DOC_KEY): WebhookVerifier
{
    return Bank::PagBrasil->webhook(new FakePagBrasilIntegration(signatureKey: $key));
}

/** Assina um webhook genérico como a PagBrasil (valores escalares menos secret/pbtoken). */
function pbSigned(array $fields): array
{
    $fields['signature'] = Signature::sign(Signature::valuesOf($fields, ['signature', 'secret', 'pbtoken']), FakePagBrasilIntegration::DOC_KEY);

    return $fields;
}

it('autentica o IPN de pagamento pelo exemplo da doc', function () {
    $ipn = pbWebhook()->parse([
        'secret' => 'SECRET', 'payment_method' => 'C', 'order' => '1234567890', 'amount_brl' => '39.50',
        'payment_status' => 'P', 'signature' => '3093a7dffa0c04e74e827d1b52ef514e',
    ]);

    expect($ipn)->toBeInstanceOf(PaymentNotification::class)
        ->and($ipn->status())->toBe(PaymentStatus::RefundProcessed)
        ->and($ipn->isRefund())->toBeTrue();
});

it('recusa IPN com secret errado, mesmo com assinatura válida', function () {
    expect(fn () => pbWebhook()->parse([
        'secret' => 'OUTRA', 'order' => '1234567890', 'amount_brl' => '39.50', 'payment_status' => 'P',
        'signature' => '3093a7dffa0c04e74e827d1b52ef514e',
    ]))->toThrow(PagBrasilSignatureException::class, 'secret');
});

it('recusa IPN com valor adulterado', function () {
    expect(fn () => pbWebhook()->parse([
        'secret' => 'SECRET', 'order' => '1234567890', 'amount_brl' => '3950.00', 'payment_status' => 'A',
        'signature' => '3093a7dffa0c04e74e827d1b52ef514e',
    ]))->toThrow(PagBrasilSignatureException::class, 'HMAC');
});

it('sem signature key, o secret basta', function () {
    $ipn = pbWebhook(key: null)->parse([
        'secret' => 'SECRET', 'order' => '1', 'amount_brl' => '1.00', 'payment_status' => 'A', 'signature' => 'lixo',
    ]);

    expect($ipn->status())->toBe(PaymentStatus::Authorized);
});

it('lê a lista de boletos pagos conferindo o HMAC do content', function () {
    $content = "<boletos_list>\r\n<boleto>\r\n<order>1234567890</order>\r\n<payment_date>10/15/2010</payment_date>\r\n"
        ."<amount_paid>29.95</amount_paid>\r\n<amount_due>29.95</amount_due>\r\n</boleto>\r\n<boleto>\r\n"
        ."<order>1234567891</order>\r\n<payment_date>10/15/2010</payment_date>\r\n<amount_paid>15.50</amount_paid>\r\n"
        ."<amount_due>16.50</amount_due>\r\n</boleto>\r\n<boleto>\r\n<order>1234567892</order>\r\n"
        ."<payment_date>10/15/2010</payment_date>\r\n<amount_paid>45.00</amount_paid>\r\n<amount_due>35.00</amount_due>\r\n"
        ."<param_url>customer_id=12345%26newsletter=yes</param_url>\r\n</boleto>\r\n</boletos_list>";

    $n = pbWebhook()->parse(['secret' => 'SECRET', 'payment_method' => 'B', 'content' => $content, 'signature' => '7bea7c5d998a4cebda5738d59458858e']);

    expect($n)->toBeInstanceOf(BoletosPaidNotification::class)
        ->and($n->boletos)->toHaveCount(3)
        ->and($n->boletos[1]->amountPaid)->toBe('15.50')
        ->and($n->boletos[1]->amountDue)->toBe('16.50')
        ->and($n->boletos[2]->paramUrl)->toBe('customer_id=12345%26newsletter=yes');
});

it('lista de boletos truncada não é aceita', function () {
    $content = '<boletos_list><boleto><order>1</order>';

    expect(fn () => pbWebhook(key: null)->parse(['secret' => 'SECRET', 'payment_method' => 'B', 'content' => $content]))
        ->toThrow(InvalidArgumentException::class);
});

it('webhooks de consentimento, favorecido e payout', function () {
    $consent = pbWebhook()->parse(pbSigned(['secret' => 'SECRET', 'action' => 'consent', 'payment_method' => 'X', 'pix_rec_id' => 'rec1', 'status' => 'authorized']));
    $payee = pbWebhook()->parse(pbSigned(['secret' => 'SECRET', 'action' => 'addpayee', 'taxid' => '91051605962', 'bank' => '341', 'status' => '1', 'name' => 'José']));
    $payout = pbWebhook()->parse(pbSigned(['secret' => 'SECRET', 'action' => 'successpayout', 'taxid' => '91051605962', 'id' => '77', 'amount' => '150.00', 'date' => '2026-10-01 10:00:00']));

    expect($consent)->toBeInstanceOf(ConsentNotification::class)->and($consent->isAuthorized())->toBeTrue()
        ->and($payee)->toBeInstanceOf(PayeeNotification::class)->and($payee->payee?->isApproved())->toBeTrue()->and($payee->payee?->name)->toBe('José')
        ->and($payout)->toBeInstanceOf(PayoutNotification::class)->and($payout->isCompleted())->toBeTrue()->and($payout->id)->toBe('77');
});

it('consentimento sem campo signature (tabela da doc) passa pelo secret', function () {
    $n = pbWebhook()->parse(['secret' => 'SECRET', 'action' => 'consent', 'payment_method' => 'X', 'pix_rec_id' => 'rec1', 'status' => 'rejected']);

    expect($n)->toBeInstanceOf(ConsentNotification::class)->and($n->isAuthorized())->toBeFalse();
});

it('webhook de assinatura em JSON cru: objeto subscription fica fora do HMAC', function () {
    $signature = Signature::sign(['subscription_paused'], FakePagBrasilIntegration::DOC_KEY);
    $body = json_encode([
        'secret' => 'SECRET', 'event_type' => 'subscription_paused',
        'subscription' => ['subscription_number' => 'P1', 'status' => 'paused', 'amount' => '110.00', 'payment' => ['installments' => 1, 'card_token' => 'C1', 'pix_consent_id' => null]],
        'signature' => $signature,
    ]);

    $n = pbWebhook()->parse((string) $body);

    expect($n)->toBeInstanceOf(SubscriptionNotification::class)
        ->and($n->eventType)->toBe('subscription_paused')
        ->and($n->subscription?->statusEnum())->toBe(SubscriptionStatus::Paused)
        ->and($n->subscription?->payment?->cardToken)->toBe('C1');
});

it('webhooks de produto (pbtoken fora do HMAC), envio cancelado e assinatura legada', function () {
    $enabled = pbWebhook()->parse(pbSigned(['secret' => 'SECRET', 'sku' => 'SKU-1234', 'frequency' => 'M', 'unit' => 1, 'billing_cycle' => 'MONTH', 'order_trigger' => 1, 'pbtoken' => 'PBTOKEN']));
    $disabled = pbWebhook()->parse(pbSigned(['secret' => 'SECRET', 'pbtoken' => 'PBTOKEN', 'action' => 'delete', 'sku' => 'SKU-1234']));
    $shipping = pbWebhook()->parse(pbSigned(['secret' => 'SECRET', 'event_type' => 'shipping_canceled', 'subscription' => 'P1', 'paid_cycle' => true,
        'shippings' => [['shipping_id' => 4471, 'schedule' => 'renewal_shippings', 'recurrence_number' => 4]]]));
    $legacy = pbWebhook()->parse(pbSigned(['secret' => 'SECRET', 'subscription' => 'P1', 'amount_brl' => '10.00', 'status' => 5, 'next_billing_date' => '2026-09-01']));

    expect($enabled)->toBeInstanceOf(ProductNotification::class)->and($enabled->isDisabled())->toBeFalse()->and($enabled->orderTrigger)->toBeTrue()
        ->and($disabled->isDisabled())->toBeTrue()
        ->and($shipping)->toBeInstanceOf(ShippingCanceledNotification::class)->and($shipping->paidCycle)->toBeTrue()->and($shipping->shippings[0]->recurrenceNumber)->toBe(4)
        ->and($legacy)->toBeInstanceOf(LegacySubscriptionNotification::class)->and($legacy->statusEnum())->toBe(SubscriptionStatus::Paused);
});

it('booleano na fonte do HMAC: aceita "1" além de "true"', function () {
    $fields = ['secret' => 'SECRET', 'event_type' => 'shipping_canceled', 'subscription' => 'P1', 'paid_cycle' => true];
    $fields['signature'] = Signature::sign(['shipping_canceled', 'P1', '1'], FakePagBrasilIntegration::DOC_KEY);

    expect(pbWebhook()->parse($fields))->toBeInstanceOf(ShippingCanceledNotification::class);
});

it('rejeita payload desconhecido e responde o acknowledgement no formato exigido', function () {
    expect(fn () => pbWebhook(key: null)->parse(['secret' => 'SECRET', 'foo' => 'bar']))->toThrow(InvalidArgumentException::class)
        ->and(WebhookVerifier::acknowledgement(new DateTimeImmutable('2026-09-01 14:32:10')))->toBe('Received successfully 2026-09-01 14:32:10')
        ->and(WebhookVerifier::acknowledgement())->toMatch('/^Received successfully \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
});
