<?php

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Gateways\Asaas\Asaas;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusSubscription;
use SistemAtc\Banks\Gateways\Asaas\Exceptions\AsaasRequestException;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\ListSubscriptionRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\CreateSubscriptionRequestDTO;

test('it creates a subscription and hydrates enums correctly', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/subscriptions' => Http::response([
            'object' => 'subscription',
            'id' => 'sub_123',
            'customer' => 'cus_123',
            'billingType' => 'BOLETO',
            'cycle' => 'MONTHLY',
            'value' => 99.90,
            'status' => 'ACTIVE',
        ], 200),
    ]);

    $dto = CreateSubscriptionRequestDTO::fromArray([
        'customer' => 'cus_123',
        'billingType' => 'BOLETO',
        'value' => 99.90,
        'nextDueDate' => '2026-03-10',
        'cycle' => 'MONTHLY',
    ]);

    $response = Asaas::make()->subscription(asaasIntegration())->createNewSubscription($dto);

    expect($response->id)->toBe('sub_123')
        ->and($response->status)->toBe(StatusSubscription::ACTIVE);
});

test('it lists subscriptions with query filters', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/subscriptions*' => Http::response([
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'limit' => 10,
            'offset' => 0,
            'data' => [[
                'object' => 'subscription',
                'id' => 'sub_123',
                'customer' => 'cus_123',
                'billingType' => 'BOLETO',
                'cycle' => 'MONTHLY',
                'value' => 99.90,
                'status' => 'ACTIVE',
            ]],
        ], 200),
    ]);

    $query = ListSubscriptionRequestDTO::fromArray([
        'offset' => 0,
        'limit' => 10,
        'status' => 'ACTIVE',
    ]);

    $response = Asaas::make()->subscription(asaasIntegration())->list($query);

    expect($response->totalCount)->toBe(1)
        ->and($response->data[0]->id)->toBe('sub_123');
});

test('it throws asaas request exception when subscription api fails', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/subscriptions' => Http::response([
            'errors' => [
                ['code' => 'invalid_cycle', 'description' => 'Ciclo invalido.'],
            ],
        ], 400),
    ]);

    $dto = CreateSubscriptionRequestDTO::fromArray([
        'customer' => 'cus_123',
        'billingType' => 'BOLETO',
        'value' => 99.90,
        'nextDueDate' => '2026-03-10',
        'cycle' => 'MONTHLY',
    ]);

    expect(fn () => Asaas::make()->subscription(asaasIntegration())->createNewSubscription($dto))
        ->toThrow(AsaasRequestException::class, 'Ciclo invalido.');
});
