<?php

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Gateways\Asaas\Asaas;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPayment;
use SistemAtc\Banks\Gateways\Asaas\Exceptions\AsaasRequestException;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\ListPaymentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\ConfirmCashRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\RefundPaymentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\CreatePaymentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\CreditCardPaymentRequestDTO;

test('it creates lean payment with summary data', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/lean/payments' => Http::response(
            asaasFixture('PaymentWithSummaryData/create_new_payment_with_summary_data_in_response'),
            200
        ),
    ]);

    $dto = CreatePaymentRequestDTO::fromArray([
        'customer' => 'cus_G7Dvo4iphUNk',
        'billingType' => 'BOLETO',
        'value' => 129.9,
        'dueDate' => '2017-06-10',
        'description' => 'Pedido 056984',
    ]);

    $response = Asaas::make()->paymentWithSummaryData(asaasIntegration())->createNewPaymentWithSummaryDataResponse($dto);

    expect($response->id)->toBe('pay_080225913252')
        ->and($response->status)->toBe(StatusPayment::PENDING);
});

test('it lists lean payments with summary data', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/lean/payments*' => Http::response(
            asaasFixture('PaymentWithSummaryData/list_payments_with_summary_data_response'),
            200
        ),
    ]);

    $query = ListPaymentRequestDTO::fromArray([
        'offset' => 0,
        'limit' => 10,
        'status' => 'PENDING',
    ]);

    $response = Asaas::make()->paymentWithSummaryData(asaasIntegration())->listPaymentsWithSummaryData($query);

    expect($response->totalCount)->toBe(2)
        ->and($response->data[0]->id)->toBe('pay_080225913252');
});

test('it creates lean payment with credit card summary data', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/lean/payments' => Http::response(
            asaasFixture('PaymentWithSummaryData/create_new_payment_with_credit_card_with_summary_data_in_response'),
            200
        ),
    ]);

    $dto = CreditCardPaymentRequestDTO::fromArray([
        'customer' => 'cus_G7Dvo4iphUNk',
        'billingType' => 'BOLETO',
        'value' => 129.9,
        'dueDate' => '2017-06-10',
        'description' => 'Pedido 056984',
        'remoteIp' => '127.0.0.1',
    ]);

    $response = Asaas::make()->paymentWithSummaryData(asaasIntegration())->createNewPaymentWithCreditCardWithSummaryDataInResponse($dto);

    expect($response->id)->toBe('pay_080225913252');
});

test('it refunds and confirms cash receipt for lean payment', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/lean/payments/*/refund' => Http::response(
            asaasFixture('PaymentWithSummaryData/refund_payment_with_summary_data_in_response'),
            200
        ),
        'https://sandbox.asaas.com/api/v3/lean/payments/*/receiveInCash' => Http::response(
            asaasFixture('PaymentWithSummaryData/confirm_cash_receipt_with_summary_data_in_response'),
            200
        ),
    ]);

    $refund = RefundPaymentRequestDTO::fromArray([
        'value' => 40.00,
        'description' => 'Refund parcial',
    ]);
    $refundResponse = Asaas::make()->paymentWithSummaryData(asaasIntegration())->refundPaymentWithSummaryDataInResponse('pay_080225913252', $refund);

    $cash = ConfirmCashRequestDTO::fromArray([
        'paymentDate' => '2026-02-27',
        'value' => 129.90,
        'notifyCustomer' => true,
    ]);
    $cashResponse = Asaas::make()->paymentWithSummaryData(asaasIntegration())->confirmCashReceiptWithSummaryDataInResponse('pay_080225913252', $cash);

    expect($refundResponse->id)->toBe('pay_080225913252')
        ->and($cashResponse->id)->toBe('pay_080225913252');
});

test('it throws asaas request exception when lean payment api fails', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/lean/payments' => Http::response([
            'errors' => [
                ['code' => 'invalid_payment', 'description' => 'Pagamento invalido.'],
            ],
        ], 400),
    ]);

    $dto = CreatePaymentRequestDTO::fromArray([
        'customer' => 'cus_G7Dvo4iphUNk',
        'billingType' => 'BOLETO',
        'value' => 129.9,
        'dueDate' => '2017-06-10',
    ]);

    expect(fn () => Asaas::make()->paymentWithSummaryData(asaasIntegration())->createNewPaymentWithSummaryDataResponse($dto))
        ->toThrow(AsaasRequestException::class, 'Pagamento invalido.');
});
