<?php

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Gateways\Asaas\Asaas;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusAnticipation;
use SistemAtc\Banks\Gateways\Asaas\Exceptions\AsaasRequestException;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\SimulateAnticipationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\ListAnticipationFilterRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\UpdateAutomaticAnticipationRequestDTO;

test('it lists anticipations with filters', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/anticipations*' => Http::response(
            asaasFixture('Anticipation/list_anticipations_response'),
            200
        ),
    ]);

    $filters = ListAnticipationFilterRequestDTO::fromArray([
        'offset' => 0,
        'limit' => 10,
        'status' => 'PENDING',
    ]);

    $response = Asaas::make()->anticipation(asaasIntegration())->listAnticipations($filters);

    expect($response->totalCount)->toBe(2)
        ->and($response->data)->toBeArray()
        ->and($response->data[0]->status)->toBe(StatusAnticipation::PENDING);
});

test('it simulates anticipation and returns calculated values', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/anticipations/simulate' => Http::response(
            asaasFixture('Anticipation/simulate_anticipation_response'),
            200
        ),
    ]);

    $simulate = SimulateAnticipationRequestDTO::fromArray(
        asaasFixture('Anticipation/simulate_anticipation_request')
    );

    $response = Asaas::make()->anticipation(asaasIntegration())->simulateAnticipation($simulate);

    expect($response->payment)->toBe('pay_626366773834')
        ->and($response->netValue)->toBe(73.68);
});

test('it updates automatic anticipation status', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/anticipations/configurations' => Http::response(
            asaasFixture('Anticipation/update_status_of_automatic_anticipation_response'),
            200
        ),
    ]);

    $update = UpdateAutomaticAnticipationRequestDTO::fromArray(
        asaasFixture('Anticipation/update_status_of_automatic_anticipation_request')
    );

    $updated = Asaas::make()->anticipation(asaasIntegration())->updateStatusAutomaticAnticipation($update);

    expect($updated->creditCardAutomaticEnabled)->toBeTrue();

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $request->method() === 'PUT'
            && str_ends_with($request->url(), '/api/v3/anticipations/configurations')
            && ($payload['creditCardAutomaticEnabled'] ?? null) === true;
    });
});

test('it throws asaas request exception when anticipation api fails', function () {
    Http::fake([
        'https://sandbox.asaas.com/api/v3/anticipations*' => Http::response([
            'errors' => [
                ['code' => 'invalid_filter', 'description' => 'Filtro invalido.'],
            ],
        ], 400),
    ]);

    $filters = ListAnticipationFilterRequestDTO::fromArray([
        'offset' => 0,
        'limit' => 10,
    ]);

    expect(fn () => Asaas::make()->anticipation(asaasIntegration())->listAnticipations($filters))
        ->toThrow(AsaasRequestException::class, 'Filtro invalido.');
});
