<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\AgoraMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\CadastroMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\CarteiraMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\ExtratoMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\PosicaoMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\SaldoMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function authedAgora(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

/** Os dois autorizadores do Bradesco — o factory autentica antes da chamada. */
function agoraTokens(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ];
}

function agoraClient(): array
{
    $i = authedAgora();

    return [HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API), $i];
}

function agoraPosicao(): PosicaoMethods
{
    [$c, $i] = agoraClient();

    return new PosicaoMethods($c, $i);
}

function agoraSaldos(): SaldoMethods
{
    [$c, $i] = agoraClient();

    return new SaldoMethods($c, $i);
}

function agoraCarteira(): CarteiraMethods
{
    [$c, $i] = agoraClient();

    return new CarteiraMethods($c, $i);
}

function agoraExtrato(): ExtratoMethods
{
    [$c, $i] = agoraClient();

    return new ExtratoMethods($c, $i);
}

function agoraCadastro(): CadastroMethods
{
    [$c, $i] = agoraClient();

    return new CadastroMethods($c, $i);
}

// ------------------------------------------------------------- posição

it('lê a posição consolidada de ações', function () {
    Http::fake(agoraTokens() + [
        '*/managers-position-mgmt/v1/consolidatedposition/equities/*' => Http::response([
            'meta' => ['totalRecords' => 1, 'totalPages' => 1, 'requestDateTime' => '2026-07-22T12:00:00Z'],
            'statusCode' => 200,
            'code' => 0,
            'description' => 'OK',
            'response' => ['success' => true, 'code' => '0', 'message' => 'OK'],
            'consolidatedPosition' => [[
                'symbol' => 'PETR4',
                'instrumentName' => 'PETROBRAS PN',
                'availableQuantity' => 100,
                'averagePrice' => 30.5,
                'currentValue' => 3200.0,
                'secutiryType' => 'ACAO',
            ]],
        ]),
    ]);

    $r = agoraPosicao()->acoes('12345678000199', 123456);

    expect($r->meta?->totalRecords)->toBe(1)
        ->and($r->response?->success)->toBeTrue()
        ->and($r->consolidatedPosition)->toHaveCount(1)
        ->and($r->consolidatedPosition[0]->symbol)->toBe('PETR4')
        ->and($r->consolidatedPosition[0]->availableQuantity)->toBe(100)
        ->and($r->consolidatedPosition[0]->currentValue)->toBe(3200.0)
        ->and($r->consolidatedPosition[0]->secutiryType)->toBe('ACAO');

    Http::assertSent(fn ($req) => str_contains($req->url(), '/consolidatedposition/equities/12345678000199/123456')
        && $req->method() === 'GET');
});

it('lê a posição consolidada de fundos', function () {
    Http::fake(agoraTokens() + [
        '*/managers-position-mgmt/v1/consolidatedposition/funds/*' => Http::response([
            'funds' => [[
                'sourceCode' => 7,
                'fund' => 'AGORA RF REFERENCIADO DI',
                'netPosition' => 15000.75,
                'rentability' => '1,05',
                'openForRescue' => true,
                'cnpj' => '00000000000191',
            ]],
        ]),
    ]);

    $r = agoraPosicao()->fundos('12345678000199', 123456);

    expect($r->funds[0]->sourceCode)->toBe(7)
        ->and($r->funds[0]->netPosition)->toBe(15000.75)
        // rentability é string no contrato: preservada como veio.
        ->and($r->funds[0]->rentability)->toBe('1,05')
        ->and($r->funds[0]->openForRescue)->toBeTrue();
});

it('lê a posição detalhada de renda fixa (response é a LISTA de títulos)', function () {
    Http::fake(agoraTokens() + [
        '*/managers-position-mgmt/v1/detailedposition/fixedIncome/*' => Http::response([
            'totalGrossValue' => 50000.0,
            'response' => [[
                'bondName' => 'CDB AGORA',
                'bondType' => 'CDB',
                'issuerName' => 'BRADESCO',
                'grossValue' => 50000.0,
                'bondRate' => '110% CDI',
                'dailyLiquidity' => true,
            ]],
        ]),
    ]);

    $r = agoraPosicao()->rendaFixaDetalhada('12345678000199', 123456);

    expect($r->totalGrossValue)->toBe(50000.0)
        ->and($r->response)->toHaveCount(1)
        ->and($r->response[0]->bondName)->toBe('CDB AGORA')
        ->and($r->response[0]->bondRate)->toBe('110% CDI')
        ->and($r->response[0]->dailyLiquidity)->toBeTrue();
});

it('monta o path do tesouro direto detalhado com tipo e vencimento', function () {
    Http::fake(agoraTokens() + [
        '*/managers-position-mgmt/v1/detailedposition/treasuryDirect/*' => Http::response([
            'detailedPosition' => [[
                'bondName' => 'TESOURO SELIC 2029',
                'maturityDate' => 20290301,
                'positionValue' => 9000.0,
                'marketType' => 'SECUNDARIO',
            ]],
        ]),
    ]);

    $r = agoraPosicao()->tesouroDiretoDetalhado('12345678000199', 123456, 'LFT', 20290301);

    expect($r->detailedPosition[0]->maturityDate)->toBe(20290301)
        ->and($r->detailedPosition[0]->positionValue)->toBe(9000.0);

    Http::assertSent(fn ($req) => str_contains(
        $req->url(),
        '/detailedposition/treasuryDirect/12345678000199/123456/LFT/20290301'
    ));
});

it('lê a posição de previdência sem código de conta', function () {
    Http::fake(agoraTokens() + [
        '*/managers-position-mgmt/v1/consolidatedposition/pension/*' => Http::response([
            'code' => '0',
            'cpf' => '12345678909',
            'proposedQuantity' => 1,
            'proposals' => [[
                'planName' => 'VGBL',
                'valueCurrentBalance' => 12345.67,
                'quantityBenefits' => 1,
                'benefits' => [[
                    'benefitCode' => 1,
                    'descriptionBenefit' => 'RENDA MENSAL',
                    'valueCurrentBalanceBenefit' => '1234,56',
                ]],
            ]],
        ]),
    ]);

    $r = agoraPosicao()->previdencia('12345678909');

    expect($r->code)->toBe('0')
        ->and($r->proposals[0]->planName)->toBe('VGBL')
        ->and($r->proposals[0]->valueCurrentBalance)->toBe(12345.67)
        ->and($r->proposals[0]->benefits[0]->valueCurrentBalanceBenefit)->toBe('1234,56');

    Http::assertSent(fn ($req) => str_contains($req->url(), '/consolidatedposition/pension/12345678909'));
});

// -------------------------------------------------------------- saldos

it('consulta o saldo disponível por POST', function () {
    Http::fake(agoraTokens() + [
        '*/managers-balance-check/v1/availableBalance/*' => Http::response([
            'availableBalance' => ['tradingSessionDate' => '2026-07-22T00:00:00Z', 'balance' => 1500.25],
        ]),
    ]);

    $r = agoraSaldos()->disponivel('12345678000199', 123456);

    expect($r->availableBalance?->balance)->toBe(1500.25);

    Http::assertSent(fn ($req) => str_contains($req->url(), '/availableBalance/12345678000199/123456')
        && $req->method() === 'POST');
});

it('consulta o saldo global', function () {
    Http::fake(agoraTokens() + [
        '*/managers-balance-check/v1/globalBalance/*' => Http::response([
            'availableBalance' => 100.0,
            'equitiesBalance' => 200.0,
            'foundsBalance' => 300.0,
            'reserveIPO' => 400.0,
            'limitCM1' => 500.0,
            'projectedBalanceSummaryResponse' => [
                'projectedBalanceD1' => 10.0,
                'tradingSessionDate' => '2026-07-22T00:00:00Z',
            ],
        ]),
    ]);

    $r = agoraSaldos()->global('12345678000199', 123456);

    expect($r->equitiesBalance)->toBe(200.0)
        ->and($r->foundsBalance)->toBe(300.0)
        ->and($r->reserveIPO)->toBe(400.0)
        ->and($r->projectedBalanceSummaryResponse?->projectedBalanceD1)->toBe(10.0);
});

it('consulta o saldo global com opção no path', function () {
    Http::fake(agoraTokens() + [
        '*/managers-balance-check/v1/globalBalance/*' => Http::response(['availableBalance' => 1.0]),
    ]);

    agoraSaldos()->globalComOpcao('12345678000199', 123456, 2);

    Http::assertSent(fn ($req) => str_contains($req->url(), '/globalBalance/12345678000199/123456/2'));
});

it('consulta o saldo de limite de margem', function () {
    Http::fake(agoraTokens() + [
        '*/managers-balance-check/v1/marginLimitBalance/*' => Http::response([
            'balance' => ['value' => 10.0, 'limit' => 100.0],
            'marginAccount' => ['firstLine' => 1.0, 'secondLine' => 2.0],
        ]),
    ]);

    $r = agoraSaldos()->limiteMargem('12345678000199', 123456);

    expect($r->balance?->limit)->toBe(100.0)
        ->and($r->marginAccount?->secondLine)->toBe(2.0);
});

// ------------------------------------------------------------ carteira

it('lê o resumo da carteira por classe de ativo', function () {
    Http::fake(agoraTokens() + [
        '*/managers-portfolio-mgmt/v1/summary/*' => Http::response([
            'referenceDate' => '2026-07-22T00:00:00Z',
            'totalGrossPatrimony' => 98765.43,
            'allocation' => [
                'equity' => ['code' => 'RV', 'description' => 'Renda Variavel', 'grossPatrimony' => 50000.0, 'percentage' => 50.6],
                'fixedIncome' => ['code' => 'RF', 'description' => 'Renda Fixa', 'grossPatrimony' => 48765.43, 'percentage' => 49.4],
            ],
        ]),
    ]);

    $r = agoraCarteira()->resumo('12345678000199', 123456);

    expect($r->totalGrossPatrimony)->toBe(98765.43)
        ->and($r->allocation?->equity?->grossPatrimony)->toBe(50000.0)
        ->and($r->allocation?->fixedIncome?->code)->toBe('RF')
        ->and($r->allocation?->derivatives)->toBeNull();
});

it('lê a lista detalhada da carteira (products keyed por sigla)', function () {
    Http::fake(agoraTokens() + [
        '*/managers-portfolio-mgmt/v1/listsummaryLessPrev/*' => Http::response([
            'result' => [
                'valuePatrimonyTotalGross' => 1000.0,
                'totalPurchaseTotal' => 900.0,
                'percentAppreciationTotal' => 11.1,
                'products' => [
                    'rv' => ['instrumentType' => 'RV', 'description' => 'Renda Variavel', 'grossPatrimony' => 600.0],
                    'fun' => ['instrumentType' => 'FUN', 'description' => 'Fundos', 'grossPatrimony' => 400.0],
                ],
            ],
        ]),
    ]);

    $r = agoraCarteira()->detalhadoSemPrevidencia('12345678000199', 123456);

    expect($r->result?->valuePatrimonyTotalGross)->toBe(1000.0)
        ->and($r->result?->products?->rv?->grossPatrimony)->toBe(600.0)
        ->and($r->result?->products?->fun?->description)->toBe('Fundos')
        ->and($r->result?->products?->coe)->toBeNull();
});

// ------------------------------------------------------------- extrato

it('lê o extrato financeiro na janela informada', function () {
    Http::fake(agoraTokens() + [
        '*/managers-statement/v1/financial/*' => Http::response([
            'statement' => [
                ['settlementDate' => '2026-07-01', 'description' => 'LIQUIDACAO', 'debitValue' => 0.0, 'creditValue' => 100.0, 'balanceValue' => 100.0],
                ['settlementDate' => '2026-07-02', 'description' => 'TAXA', 'debitValue' => 5.0, 'creditValue' => 0.0, 'balanceValue' => 95.0],
            ],
        ]),
    ]);

    $r = agoraExtrato()->financeiro('12345678000199', 123456, new DateTimeImmutable('2026-07-01'), '2026-07-31');

    expect($r->statement)->toHaveCount(2)
        ->and($r->statement[1]->debitValue)->toBe(5.0)
        ->and($r->statement[1]->balanceValue)->toBe(95.0);

    Http::assertSent(fn ($req) => str_contains($req->url(), '/financial/12345678000199/123456/2026-07-01/2026-07-31'));
});

it('lê as taxas de margem', function () {
    Http::fake(agoraTokens() + [
        '*/managers-statement/v1/marginlimit-fees/*' => Http::response([
            'fees' => [[
                'settlementDate' => '2026-07-10',
                'usedLimit' => 1000.0,
                'interestValue' => 3.5,
                'iof' => 0.9,
                'additionalIOF' => 0.38,
            ]],
        ]),
    ]);

    $r = agoraExtrato()->taxasMargem('12345678000199', 123456, '2026-07-01', '2026-07-31');

    expect($r->fees[0]->usedLimit)->toBe(1000.0)
        ->and($r->fees[0]->additionalIOF)->toBe(0.38);
});

// ------------------------------------------------------------ cadastro

it('busca os CBLCs do CPF/CNPJ', function () {
    Http::fake(agoraTokens() + [
        '*/managers-cust-access-info/v1/searchcblc/*' => Http::response(['cblcs' => [123456, 654321]]),
    ]);

    expect(agoraCadastro()->cblcs('12345678909')->cblcs)->toBe([123456, 654321]);
});

it('lê os dados financeiros e bancários do cadastro', function () {
    Http::fake(agoraTokens() + [
        '*/managers-cust-financial-info-update/v1/FinancialData/*' => Http::response([
            'profession' => 'EMPRESARIO',
            'working' => true,
            'companyName' => 'SOLDIERS',
            'incomeData' => ['monthlySalaryAmount' => 10000.0, 'valuePropertys' => 500000.0],
            'bankAccountsData' => [[
                'bank' => '237',
                'agency' => '1234',
                'account' => '567890',
                'digit' => '1',
                'mainAccount' => 'S',
            ]],
        ]),
    ]);

    $r = agoraCadastro()->dadosFinanceiros('12345678909');

    expect($r->working)->toBeTrue()
        ->and($r->incomeData?->monthlySalaryAmount)->toBe(10000.0)
        ->and($r->bankAccountsData[0]->mainAccount)->toBe('S');
});

it('inverte a ordem cblc/cpf no modelo de liquidação', function () {
    Http::fake(agoraTokens() + [
        '*/managers-settlement/v1/ModelSettlement/*' => Http::response([
            'regress' => ['status' => 1, 'message' => 'BRADESCO'],
        ]),
    ]);

    $r = agoraCadastro()->modeloLiquidacao(123456, '12345678909');

    expect($r->regress?->status)->toBe(1)
        ->and($r->regress?->message)->toBe('BRADESCO');

    Http::assertSent(fn ($req) => str_contains($req->url(), '/ModelSettlement/123456/12345678909'));
});

it('lê o perfil do investidor (suitability)', function () {
    Http::fake(agoraTokens() + [
        '*/managers-suitability/v1/CustomerProfile/*' => Http::response([
            'profile' => 'MODERADO',
            'isApicEnabled' => true,
            'investorProfileCode' => 2,
            'investorProfileDescription' => 'Moderado',
            'score' => 42.5,
            'portfolios' => [[
                'portfolioManagementCodeApi' => 9,
                'portfolioManagementDescriptionApi' => 'CARTEIRA X',
                'conformityIdentifierCode' => 1,
            ]],
        ]),
    ]);

    $r = agoraCadastro()->perfilInvestidor('12345678909');

    expect($r->profile)->toBe('MODERADO')
        ->and($r->isApicEnabled)->toBeTrue()
        ->and($r->score)->toBe(42.5)
        ->and($r->portfolios[0]->portfolioManagementDescriptionApi)->toBe('CARTEIRA X');
});

it('lê a situação cadastral (vencimentos)', function () {
    Http::fake(agoraTokens() + [
        '*/managers-expiration-alert/v1/expirationAlert/*' => Http::response([
            'registration' => ['status' => 0, 'expiration' => '2027-01-31'],
            'profile' => ['status' => 1, 'expiration' => '2026-12-31'],
        ]),
    ]);

    $r = agoraCadastro()->situacaoCadastral('12345678909', 123456);

    expect($r->registration?->expiration)->toBe('2027-01-31')
        ->and($r->profile?->status)->toBe(1);
});

it('lê o nome do cliente', function () {
    Http::fake(agoraTokens() + [
        '*/managers-cust-aggregated-data-spb/v1/clientfulldata/*' => Http::response(['customerName' => 'JOAO CARLOS']),
    ]);

    expect(agoraCadastro()->nomeCliente('12345678909', 123456)->customerName)->toBe('JOAO CARLOS');
});

// ------------------------------------------------------------- fachada

it('a fachada roteia pro grupo certo reaproveitando o mesmo client', function () {
    Http::fake(agoraTokens() + [
        '*/managers-balance-check/v1/equitiesBalance/*' => Http::response([
            'equities' => ['tradingSessionDate' => '2026-07-22T00:00:00Z', 'balance' => 77.0],
        ]),
        '*/managers-cust-access-info/v1/searchcblc/*' => Http::response(['cblcs' => [1]]),
    ]);

    [$c, $i] = agoraClient();
    $agora = new AgoraMethods($c, $i);

    expect($agora->saldos()->patrimonio('12345678000199', 123456)->equities?->balance)->toBe(77.0)
        ->and($agora->cadastro()->cblcs('12345678909')->cblcs)->toBe([1])
        // sub-acessores são memoizados
        ->and($agora->posicao())->toBe($agora->posicao());
});
