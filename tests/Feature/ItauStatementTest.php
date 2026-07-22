<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Itau\DTO\Response\Statement\BalancesResponse;
use SistemAtc\Banks\Itau\DTO\Response\Statement\InterestBearingIncome;
use SistemAtc\Banks\Itau\DTO\Response\Statement\JudicialOrdersResponse;
use SistemAtc\Banks\Itau\DTO\Response\Statement\StatementEvent;
use SistemAtc\Banks\Itau\DTO\Response\Statement\StatementResponse;
use SistemAtc\Banks\Itau\Endpoints\Statement\StatementMethods;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/** @param array<string, mixed> $settings */
function statementAuthed(array $settings = []): FakeBankIntegration
{
    $i = new FakeBankIntegration(settings: $settings);
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

function statementMethods(FakeBankIntegration $i): StatementMethods
{
    return new StatementMethods(HttpClientFactory::make($i), $i);
}

it('consulta extrato (GET /statements/{id}) hidratando events/balances/pagination + headers', function () {
    Http::fake([
        '*/account-statement/v1/statements/150001234567?*' => Http::response([
            'data' => [[
                'events' => [[
                    'id' => '104e2ce6-4b1d-3fba-adf0-694fde806773',
                    'type' => 'lancamento',
                    'operation' => 'C',
                    'reversal' => false,
                    'date' => ['event' => '2024-04-25T02:59:00Z', 'accounting' => '2024-04-24'],
                    'literal' => ['code' => '9507', 'shortened' => 'SISPAG PIX PIX', 'complete' => 'SISPAG PIX PIX'],
                    'amount' => ['value' => 500.00, 'currency' => 'BRL'],
                    'counterpart' => ['name' => 'BRUNO E. S. FRAUCHES', 'person' => 'FISICA'],
                    'origin' => ['type' => 'PIX', 'operation' => 'PIX_EMISSAO'],
                ]],
                'balances' => [[
                    'type' => 'saldo_disponivel',
                    'date' => ['event' => '2024-06-24T23:59:59.999999999-03:00'],
                    'literal' => ['shortened' => 'SALDO TOTAL DISPONÍVEL DIA', 'complete' => 'SALDO TOTAL DISPONÍVEL DIA'],
                    'amount' => ['value' => 63.97, 'currency' => 'BRL'],
                ]],
            ]],
            'pagination' => [
                'links' => ['next' => '/statements/150001234567?page=2', 'previous' => ''],
                'page' => 1,
                'total_pages' => 10,
                'total_elements' => 9109,
                'page_size' => 1000,
            ],
        ]),
    ]);

    $res = statementMethods(statementAuthed())->extrato('150001234567', '2023-01-01', '2026-06-26');

    expect($res)->toBeInstanceOf(StatementResponse::class)
        ->and($res->data)->toHaveCount(1)
        ->and($res->data[0]->events[0]->operation)->toBe('C')
        ->and($res->data[0]->events[0]->amount?->value)->toBe(500.00)
        ->and($res->data[0]->events[0]->counterpart?->name)->toBe('BRUNO E. S. FRAUCHES')
        ->and($res->data[0]->events[0]->date?->accounting)->toBe('2024-04-24')
        ->and($res->data[0]->balances[0]->type)->toBe('saldo_disponivel')
        ->and($res->pagination?->totalElements)->toBe(9109);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/account-statement/v1/statements/150001234567')
        && str_contains($r->url(), 'start_date=2023-01-01')
        && str_contains($r->url(), 'type=current_account')
        && $r->hasHeader('x-itau-apikey', 'cli')
        && $r->hasHeader('x-itau-correlationID')
        && $r->hasHeader('Authorization', 'Bearer TOK'));
});

it('periodo() resolve o statement_id das settings e achata os events', function () {
    Http::fake([
        '*/account-statement/v1/statements/*' => Http::response([
            'data' => [[
                'events' => [
                    ['id' => 'a', 'operation' => 'C', 'amount' => ['value' => 10.0]],
                    ['id' => 'b', 'operation' => 'D', 'amount' => ['value' => 20.0]],
                ],
            ]],
        ]),
    ]);

    $eventos = statementMethods(statementAuthed(['statement_id' => '150001234567']))
        ->periodo('2026-01-01', '2026-01-31');

    expect($eventos)->toHaveCount(2)
        ->and($eventos[0])->toBeInstanceOf(StatementEvent::class)
        ->and($eventos[0]->id)->toBe('a')
        ->and($eventos[1]->operation)->toBe('D');
});

it('periodo() estoura quando nao ha statement_id nem agencia/conta/dac', function () {
    Http::fake(['*' => Http::response([])]);

    statementMethods(statementAuthed())->periodo('2026-01-01', '2026-01-31');
})->throws(RuntimeException::class);

it('consulta saldos (GET /balances) com summary consolidado', function () {
    Http::fake([
        '*/account-statement/v1/balances' => Http::response([
            'data' => [[
                'statementId' => '150000999999',
                'balances' => [
                    ['type' => 'saldo_disponivel', 'amount' => ['value' => 100.00, 'currency' => 'BRL']],
                    ['type' => 'saldo_bloqueado', 'amount' => ['value' => 0.00, 'currency' => 'BRL']],
                ],
            ]],
            'summary' => [
                ['type' => 'saldo_total', 'amount' => ['value' => 100.00, 'currency' => 'BRL']],
            ],
        ]),
    ]);

    $res = statementMethods(statementAuthed())->saldos();

    expect($res)->toBeInstanceOf(BalancesResponse::class)
        ->and($res->data[0]->statementId)->toBe('150000999999')
        ->and($res->data[0]->balances)->toHaveCount(2)
        ->and($res->data[0]->balances[0]->amount?->value)->toBe(100.00)
        ->and($res->summary[0]->type)->toBe('saldo_total');
});

it('trata HTTP 206 de /balances (conta com error, summary vazio)', function () {
    Http::fake([
        '*/account-statement/v1/balances' => Http::response([
            'data' => [
                ['statementId' => '150000999997', 'error' => ['error' => 'Serviço indisponível']],
            ],
            'summary' => [],
        ], 206),
    ]);

    $res = statementMethods(statementAuthed())->saldos();

    expect($res->data[0]->statementId)->toBe('150000999997')
        ->and($res->data[0]->error?->error)->toBe('Serviço indisponível')
        ->and($res->summary)->toBe([]);
});

it('consulta rendimentos de aplicacao automatica (interest-bearing-accounts)', function () {
    Http::fake([
        '*/account-statement/v1/statements/150001234567/interest-bearing-accounts*' => Http::response([
            'data' => [
                ['date' => '2024-05-01', 'grossAmountValue' => '22700.62', 'ir' => '0.23', 'iof' => '0.73'],
                ['date' => '2024-05-02', 'grossAmountValue' => '201148.88', 'ir' => '15.24'],
            ],
        ]),
    ]);

    $rend = statementMethods(statementAuthed())->rendimentos('150001234567', '2024-05-01', '2024-05-31');

    expect($rend)->toHaveCount(2)
        ->and($rend[0])->toBeInstanceOf(InterestBearingIncome::class)
        ->and($rend[0]->grossAmountValue)->toBe('22700.62')
        ->and($rend[1]->ir)->toBe('15.24');
});

it('consulta ordens judiciais (judicial-orders) paginadas', function () {
    Http::fake([
        '*/account-statement/v1/statements/150001234567/judicial-orders*' => Http::response([
            'data' => [[
                'blockOrderId' => '000120263333333001000010000',
                'personType' => 'F',
                'blockType' => 'VALOR',
                'blockStatus' => 'CUMPRIDA',
                'blockOrderValue' => 150000.00,
                'courtCode' => 43610,
                'courtPhoneNumber' => null,
            ]],
            'pagination' => ['page' => 1, 'total_pages' => 1, 'total_elements' => 1],
        ]),
    ]);

    $res = statementMethods(statementAuthed())->ordensJudiciais('150001234567', '2026-01-29', '2026-02-04');

    expect($res)->toBeInstanceOf(JudicialOrdersResponse::class)
        ->and($res->data[0]->blockStatus)->toBe('CUMPRIDA')
        ->and($res->data[0]->blockOrderValue)->toBe(150000.00)
        ->and($res->data[0]->courtCode)->toBe(43610)
        ->and($res->data[0]->courtPhoneNumber)->toBeNull()
        ->and($res->pagination?->totalElements)->toBe(1);
});
