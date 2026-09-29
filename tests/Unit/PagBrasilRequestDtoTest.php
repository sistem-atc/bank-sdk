<?php

declare(strict_types=1);

use SistemAtc\Banks\PagBrasil\DTO\Request\Checkout\PaymentLink;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Address;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\AutomaticPixTerms;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Card;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Product;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\RefundBankAccount;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\RefundPixKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\ThreeDSecure;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Wallet;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\BoletoOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\CardOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\DebitOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\PixOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\Refund;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\Cancellation;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ChargeFilter;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\NewSubscription;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\PaymentMethodChange;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ProductFilter;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ProductRuleSettings;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ProductSettings;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\RecurrenceItem;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\RecurrenceItemChange;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ShippingChange;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ShippingItemInput;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ShippingsUpdate;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\StandingItem;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\SubscriptionFilter;
use SistemAtc\Banks\PagBrasil\DTO\Request\Payout\PayeeData;
use SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico\AutomaticPixConsent;
use SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico\PixOrderWithConsent;
use SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico\RecurringCharge;
use SistemAtc\Banks\PagBrasil\Enums\CancellationOption;
use SistemAtc\Banks\PagBrasil\Enums\CaptureMode;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\PagBrasil\Enums\PixKeyType;
use SistemAtc\Banks\PagBrasil\Enums\RecurrenceCycle;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionPaymentMethod;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;

function pbCustomer(): Customer
{
    return new Customer('José da Silva', '529.982.247-25', 'jose@example.com', '11 3328.9999');
}

function pbAddress(): Address
{
    return new Address('Av. Paulista, 100', '01311-100', 'São Paulo', 'sp');
}

function pbProducts(): array
{
    return [new Product('SKU-1', 39.5)];
}

it('cliente e endereço normalizam documento, CEP e UF e viram customer_*/address_*', function () {
    expect(pbCustomer()->toArray())->toBe([
        'customer_name' => 'José da Silva', 'customer_taxid' => '52998224725',
        'customer_email' => 'jose@example.com', 'customer_phone' => '11 3328.9999',
    ])->and(pbAddress()->toArray())->toBe([
        'address_street' => 'Av. Paulista, 100', 'address_zip' => '01311100',
        'address_city' => 'São Paulo', 'address_state' => 'SP',
    ])->and((new Customer('Empresa', '12.abc.345/01de-35'))->taxid)->toBe('12ABC34501DE35');
});

it('valida documento, CEP e UF', function (Closure $build) {
    expect($build)->toThrow(InvalidArgumentException::class);
})->with([
    'CPF curto' => fn () => new Customer('X', '123'),
    'CEP' => fn () => new Address('R', '123', 'C', 'SP'),
    'UF' => fn () => new Address('R', '01311100', 'C', 'São'),
]);

it('pedido de cartão serializa com os nomes da doc e o payment_method fixo', function () {
    $order = new CardOrder(
        order: 'PED-1', productName: 'Whey', products: pbProducts(), customer: pbCustomer(), address: pbAddress(),
        amountBrl: 39.5, installments: 3,
        card: new Card('JOSE DA SILVA', '4984123412341234', '12/29', '123'),
        softDescriptor: 'SOLDIERS',
        threeDSecure: new ThreeDSecure('success', 'cavv', '2', '02', 'ref-1', 'xid'),
    );

    $data = $order->toArray();

    expect($data)->toMatchArray([
        'payment_method' => 'C', 'order' => 'PED-1', 'product_name' => 'Whey',
        'products' => [['sku' => 'SKU-1', 'amount' => 39.5, 'quantity' => 1, 'discount' => 0.0]],
        'customer_taxid' => '52998224725', 'address_zip' => '01311100',
        'amount_brl' => '39.50', 'cc_installments' => 3,
        'cc_holder' => 'JOSE DA SILVA', 'cc_number' => '4984123412341234', 'cc_expiration' => '12/29', 'cc_cvv' => '123',
        'cc_save' => false, 'cc_auth' => '0', 'soft_descriptor' => 'SOLDIERS',
        'auth3ds_type' => 'success', 'auth3ds_reference_id' => 'ref-1',
    ])->and($data)->not->toHaveKeys(['card', 'customer', 'visitor_id', 'cc_prevsaved']);

    // Número do cartão nunca aparece em dump.
    expect(print_r(new Card('A', '4984123412341234', '12/29', '123'), true))->not->toContain('4984123412341234');
});

it('pedido de cartão exige exatamente uma fonte do cartão', function () {
    $build = fn (...$source) => new CardOrder('P', 'X', pbProducts(), pbCustomer(), pbAddress(), 10, ...$source);

    expect(fn () => $build())->toThrow(InvalidArgumentException::class, 'UMA fonte')
        ->and(fn () => $build(card: new Card('A', '1', '12/29'), previousOrder: 'P0'))->toThrow(InvalidArgumentException::class)
        ->and($build(previousOrder: 'P0', saveCard: true)->toArray())->toMatchArray(['cc_prevsaved' => 'P0', 'cc_save' => true])
        ->and($build(wallet: new Wallet('GP', '{"x":1}'))->toArray())->toMatchArray(['wallet_type' => 'GP', 'wallet_payload' => '{"x":1}'])
        ->and(fn () => $build(previousOrder: 'P0', capture: CaptureMode::Capture))->toThrow(InvalidArgumentException::class, 'capturar');
});

it('débito, boleto e Pix fixam o meio de pagamento', function () {
    $debit = new DebitOrder('D1', 'X', pbProducts(), pbCustomer(), pbAddress(), '10', new Card('A', '5555666677778884', '12/29'),
        new ThreeDSecure('success', 'c', '2', '05', 'r', 'x'));
    $boleto = new BoletoOrder('B1', 'X', pbProducts(), pbCustomer(), pbAddress(), '1.234,56', expirationDays: 3);
    $pix = new PixOrder('X1', 'X', pbProducts(), pbCustomer(), pbAddress(), 10, expirationMinutes: 60, urlReturn: 'https://x');

    expect($debit->toArray())->toMatchArray(['payment_method' => 'D', 'cc_installments' => '1', 'amount_brl' => '10.00'])
        ->and($debit->toArray())->not->toHaveKey('cc_cvv')
        ->and($boleto->toArray())->toMatchArray(['payment_method' => 'B', 'bol_expiration' => 3, 'amount_brl' => '1234.56'])
        ->and($pix->toArray())->toMatchArray(['payment_method' => 'X', 'pix_expiration' => 60, 'url_return' => 'https://x'])
        ->and(fn () => new PixOrder('X', 'X', [], pbCustomer(), pbAddress(), 1, expirationMinutes: 9000))->toThrow(InvalidArgumentException::class);
});

it('estorno com destino bancário ou chave Pix', function () {
    expect((new Refund('P1', 50, new RefundBankAccount('001', '1234', '12345678-0')))->toArray())->toEqual([
        'order' => 'P1', 'amount_refunded' => '50.00', 'suspicious' => false,
        'customer_bank' => '001', 'customer_branch' => '1234', 'customer_account' => '12345678-0',
    ])->and((new Refund('P1', '9.9', new RefundPixKey(PixKeyType::Alternative, 'a2aa-…'), suspicious: true))->toArray())->toMatchArray([
        'amount_refunded' => '9.90', 'customer_pix_key_type' => 'alternative', 'suspicious' => true,
    ]);
});

it('Link de Pagamento traduz opções, flags de dado pedido, 3DS e Pix Automático', function () {
    $link = new PaymentLink(
        order: 'L1', amountBrl: 99, paymentOptions: [PaymentMethod::CreditCard, PaymentMethod::Pix], walletOptions: ['AP', 'GP'],
        expirationDate: new DateTimeImmutable('2026-10-31'), requestAddress: false, maxInstallments: 6,
        require3dsCredit: true, proceedOnCredit3dsFailure: false, fxCurrency: 'USD', fxAmount: '18.5',
        automaticPix: new AutomaticPixTerms(RecurrenceCycle::Monthly, '2026-11-05', 'Assinatura Whey'),
        address: new Address('Rua A', '01311100', 'SP', 'SP', number: '10', complement: 'ap 2', neighborhood: 'Centro'),
    );

    expect($link->toArray())->toMatchArray([
        'order' => 'L1', 'amount_brl' => '99.00', 'payment_option' => 'C,X', 'wallet_option' => 'AP,GP',
        'payment_link_expiration_date' => '2026-10-31', 'address_requested' => '0', 'max_installments' => 6,
        'cc_authentication' => '2', 'cc_authentication_onfailure' => '1', 'fx_currency' => 'USD', 'fx_amount' => '18.50',
        'pix_rec' => '1', 'pix_rec_cycle' => 'monthly', 'pix_rec_first_recurrence' => '2026-11-05',
        'pix_rec_description' => 'Assinatura Whey', 'pix_rec_retry' => true,
        'address_number' => '10', 'address_number_complement' => 'ap 2', 'address_neighborhood' => 'Centro',
    ])->and($link->toArray())->not->toHaveKeys(['phone_requested', 'email_requested', 'dc_authentication', 'products']);

    expect(fn () => new PaymentLink('L', 1, expirationDays: 3, expirationDate: '2026-01-01'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => new PaymentLink('L', 1, fxCurrency: 'USD'))->toThrow(InvalidArgumentException::class);
});

it('Pix Automático: consentimento, pagamento com consentimento e cobrança recorrente', function () {
    $terms = new AutomaticPixTerms(RecurrenceCycle::Quarterly, new DateTimeImmutable('2026-12-01'), minimumAmount: 50, expiration: '2027-12-01');
    $pix = new PixOrder('X1', 'Plano', pbProducts(), pbCustomer(), pbAddress(), 50);

    expect((new AutomaticPixConsent('Ana', '529.982.247-25', $terms))->toArray())->toBe([
        'payer_name' => 'Ana', 'payer_taxid' => '52998224725', 'pix_rec_cycle' => 'quarterly',
        'pix_rec_first_recurrence' => '2026-12-01', 'pix_rec_expiration' => '2027-12-01',
        'pix_rec_minimum_amount' => '50.00', 'pix_rec_retry' => true,
    ])->and((new PixOrderWithConsent($pix, $terms))->toArray())->toMatchArray([
        'pix_rec' => '1', 'payment_method' => 'X', 'order' => 'X1', 'pix_rec_cycle' => 'quarterly',
    ])->and((new RecurringCharge('R2', 'Plano', pbProducts(), pbCustomer(), pbAddress(), 50, 'rec1', '2026-12-01'))->toArray())->toMatchArray([
        'payment_method' => 'X', 'pix_rec_id' => 'rec1', 'pix_rec_recurrence_date' => '2026-12-01',
    ]);
});

it('favorecido do Payout: payee_* e checagem do cadastro', function () {
    $payee = new PayeeData('910.516.059-62', name: 'José', bank: '341', branch: '1234', account: '1234568-0', accountType: PayeeData::CHECKING);

    expect($payee->toArray())->toBe([
        'payee_taxid' => '91051605962', 'payee_name' => 'José', 'payee_bank' => '341',
        'payee_branch' => '1234', 'payee_account' => '1234568-0', 'payee_account_type' => 1,
    ])->and($payee->missingForCreate())->toBe(['description', 'email', 'phone', 'street', 'zip', 'city', 'state']);
});

it('PagStream: assinatura nova, filtros, cancelamento e forma de pagamento', function () {
    $new = new NewSubscription(pbProducts(), 'Whey mensal', pbCustomer(), pbAddress(), 39.5, '2026-11-01', 'M', limit: 12,
        card: new Card('A', '4984123412341234', '12/29', '123'), installments: 1);

    expect($new->toArray())->toMatchArray([
        'products' => [['sku' => 'SKU-1', 'amount' => 39.5, 'quantity' => 1, 'discount' => 0.0]],
        'amount_brl' => '39.50', 'next_billing_date' => '2026-11-01', 'billing_cycle' => 'M', 'limit' => 12,
        'cc_number' => '4984123412341234', 'cc_installments' => 1,
    ]);

    expect((new SubscriptionFilter(taxId: '12345678909', status: SubscriptionStatus::Paused, perPage: 50))->toArray())
        ->toBe(['tax_id' => '12345678909', 'status' => 'paused', 'per_page' => 50])
        ->and(fn () => new SubscriptionFilter())->toThrow(InvalidArgumentException::class)
        ->and(fn () => new SubscriptionFilter(email: 'a@b', perPage: 101))->toThrow(InvalidArgumentException::class);

    expect((new Cancellation(CancellationOption::Other, 'Mudou de país', 'ops'))->toArray())
        ->toBe(['cancellation_option' => 'other', 'cancellation_reason' => 'Mudou de país', 'canceled_by' => 'ops'])
        ->and(fn () => new Cancellation(canceledBy: str_repeat('x', 33)))->toThrow(InvalidArgumentException::class);

    expect((new PaymentMethodChange(SubscriptionPaymentMethod::CreditCard, cardToken: 'tok', installments: 3))->toArray())
        ->toBe(['payment_method' => 'credit_card', 'card_token' => 'tok', 'installments' => 3])
        ->and(fn () => new PaymentMethodChange(SubscriptionPaymentMethod::AutomaticPix))->toThrow(InvalidArgumentException::class, 'pixConsentToken')
        ->and(fn () => new PaymentMethodChange())->toThrow(InvalidArgumentException::class)
        ->and(print_r(new PaymentMethodChange(SubscriptionPaymentMethod::CreditCard, cardToken: 'SEGREDO'), true))->not->toContain('SEGREDO');
});

it('PagStream: itens de recorrência com dinheiro em string decimal', function () {
    expect((new RecurrenceItem('SKU-1', 2, 24.95, 10))->toArray())
        ->toBe(['product_sku' => 'SKU-1', 'quantity' => 2, 'price' => '24.95', 'discount' => '10.00'])
        ->and((new RecurrenceItemChange(price: 0))->toArray())->toBe(['price' => '0.00'])
        ->and((new StandingItem('129.9', 1, '0'))->toArray())->toBe(['amount_brl' => '129.90', 'quantity' => 1, 'discount' => '0.00'])
        ->and(fn () => new RecurrenceItemChange())->toThrow(InvalidArgumentException::class)
        ->and(fn () => new RecurrenceItem('S', 0))->toThrow(InvalidArgumentException::class);
});

it('PagStream: envios com cancelamento e troca de itens', function () {
    $update = new ShippingsUpdate(
        firstBuy: [new ShippingChange(551, scheduledFor: '2026-09-10')],
        renewal: [4 => [ShippingChange::cancelar(902), new ShippingChange(903, items: [new ShippingItemInput('SKU-1', 2, 44.9)])]],
        confirmPaidCycleEdit: true,
    );

    expect($update->toArray())->toBe([
        'first_buy_shippings' => [['shipping_id' => 551, 'scheduled_for' => '2026-09-10']],
        'renewal_shippings' => ['4' => [
            ['status' => 'canceled', 'shipping_id' => 902],
            ['shipping_id' => 903, 'items' => [['sku' => 'SKU-1', 'quantity' => 2, 'amount' => '44.90']]],
        ]],
        'confirm_paid_cycle_edit' => true,
    ]);

    expect(fn () => new ShippingChange(1))->toThrow(InvalidArgumentException::class)
        ->and(fn () => new ShippingsUpdate())->toThrow(InvalidArgumentException::class);
});

it('PagStream: catálogo distingue "manter" de "apagar" e filtros viram query', function () {
    $settings = new ProductSettings(status: 'active', onCustomerArea: true, rules: [
        new ProductRuleSettings(id: 12, billingDay: 10),
        new ProductRuleSettings(id: 13, clearShippingCycle: true, clearBillingDay: true),
        new ProductRuleSettings(billingCycle: 'W', limit: 12),
    ]);

    expect(json_encode($settings->toArray()))->toBe(
        '{"status":"active","on_customer_area":true,"rules":[{"id":12,"billing_day":10},{"shipping_cycle":null,"billing_day":null,"id":13},{"billing_cycle":"W","limit":12}]}'
    )->and(fn () => new ProductRuleSettings(limit: 1))->toThrow(InvalidArgumentException::class, 'billingCycle');

    expect((new ProductFilter(status: 'active', billingCycles: ['M', 'W'], onCustomerArea: false))->toArray())
        ->toBe(['billing_cycle' => 'M,W', 'on_customer_area' => 'false', 'status' => 'active'])
        ->and((new ChargeFilter(status: 'error', dateFrom: new DateTimeImmutable('2026-07-01'), perPage: 24))->toArray())
        ->toBe(['status' => 'error', 'date_from' => '2026-07-01', 'per_page' => 24]);
});
