<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularBaMethods;
use SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularMethods;
use SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularMgMethods;
use SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularPrMethods;
use SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularSpMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function authedDebitoVeicular(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function debitoVeicularTokens(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ];
}

/** @param class-string $class */
function debitoVeicularMethods(string $class): object
{
    $i = authedDebitoVeicular();

    return new $class(
        HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API),
        $i,
    );
}

// ---------------------------------------------------------------- SP

it('lista debitos por renavam em SP e hidrata a lista de tributos', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-sp/renavam/lista-debitos/listaDebitosVeicularesSP*' => Http::response([
            'codigoRetorno' => 0,
            'codigoMensagem' => 'ARCD0009',
            'codigoRenavam' => 1222953460,
            'codigoPlaca' => 'GFA1I11',
            'nomeProprietario' => 'ROGERIO APARECI',
            'quantidadeOcorrencia' => 1,
            'lista' => [[
                'nomeTributo' => 'IPVA',
                'anoTributo' => '2022',
                'codigoTributo' => 1,
                'valorTributo' => 1017.42,
                'indicadorPagamentoTributo' => 'N',
            ]],
        ]),
    ]);

    /** @var DebitoVeicularSpMethods $sp */
    $sp = debitoVeicularMethods(DebitoVeicularSpMethods::class);
    $r = $sp->listarDebitosRenavam([
        'codigoRenavam' => 1222953460,
        'digitoConta' => 7,
        'codigoConta' => 999,
        'codigoCanal' => 536,
        'codigoTributo' => 60,
        'codigoUf' => 'SP',
        'codigoAgencia' => 145,
        'validacaolistaPositiva' => 'N',
    ]);

    expect($r->codigoPlaca)->toBe('GFA1I11')
        ->and($r->codigoRenavam)->toBe(1222953460)
        ->and($r->lista)->toHaveCount(1)
        ->and($r->lista[0]->nomeTributo)->toBe('IPVA')
        ->and($r->lista[0]->valorTributo)->toBe(1017.42);
});

it('efetua pagamento por renavam em SP no path exato da spec', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-sp/renavam/efetua-pagamento/efetuaPagamentoSp*' => Http::response([
            'codigoRetorno' => 0,
            'codigoMensagem' => 'ARCD2782',
            'nsuBanco' => 29133264,
            'nsuProdesp' => 10000002,
            'valorTotal' => 2592.27,
            'listaMsgs' => [['codigoComprovante' => 1334, 'mensagemComprovante' => 'Comprovante']],
            'listaMulta' => [['numeroGuiaMulta' => '5A4822244', 'valorMulta' => 217.54]],
        ]),
    ]);

    /** @var DebitoVeicularSpMethods $sp */
    $sp = debitoVeicularMethods(DebitoVeicularSpMethods::class);
    $r = $sp->efetuarPagamentoRenavam([
        'lista' => [['codigoTributo' => 302, 'valorTributo' => 2592.27]],
        'dataPagamento' => '17.02.2025',
        'identificacaoPeriferico' => '1',
        'codigoRenavam' => 1366489915,
        'codigoCanal' => 536,
        'codigoUf' => 'SP',
        'nsuBanco' => 29133264,
        'identificacaoFuncao' => 'C',
        'numeroConta' => 999,
        'digitoConta' => 7,
        'codigoTributo' => 175,
        'tipoConta' => 'C',
        'codigoAgencia' => 145,
        'validacaoListaPositiva' => 'N',
        'quantidadeOcorrencia' => 1,
    ]);

    expect($r->nsuBanco)->toBe(29133264)
        ->and($r->valorTotal)->toBe(2592.27)
        ->and($r->listaMsgs[0]->codigoComprovante)->toBe(1334)
        ->and($r->listaMulta[0]->numeroGuiaMulta)->toBe('5A4822244');

    Http::assertSent(fn ($req) => str_contains($req->url(), '/v1/debitos-veiculares-sp/renavam/efetua-pagamento/efetuaPagamentoSp')
        && $req->method() === 'POST'
        && $req['nsuBanco'] === 29133264);
});

it('efetua pagamento de taxas do detran SP', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-sp/taxas/efetua-pagamento/efetuaPagamentoTaxas*' => Http::response([
            'codigoRetorno' => 0,
            'codigoMensagem' => 'ARCD2388',
            'descricaoMensagem' => 'PAGAMENTO EFETUADO COM SUCESSO',
            'nsuBanco' => 282746,
            'valorTotal' => 116.5,
            'lista' => [['codigoMsgRodape' => 1334, 'descricaoMsgRodape' => 'Comprovante']],
        ]),
    ]);

    /** @var DebitoVeicularSpMethods $sp */
    $sp = debitoVeicularMethods(DebitoVeicularSpMethods::class);
    $r = $sp->efetuarPagamentoTaxas([
        'nsuBanco' => 282746,
        'identificacaoFuncao' => 'C',
        'codigoServico' => 1,
        'codigoSubServico' => 1,
        'codigoReceita' => 4250,
        'valorTaxaDetran' => 48.62,
        'valorTarifaPostagem' => 10.01,
        'valorTotal' => 58.63,
    ]);

    expect($r->descricaoMensagem)->toBe('PAGAMENTO EFETUADO COM SUCESSO')
        ->and($r->nsuBanco)->toBe(282746)
        ->and($r->lista[0]->descricaoMsgRodape)->toBe('Comprovante');
});

it('lista debitos de veiculo zero km em SP pelo cpf/cnpj', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-sp/primeiro-veiculo/lista-debitos/consultarDebitosVeicularesSP*' => Http::response([
            'codigoRetorno' => 0,
            'codigoMensagem' => 'ARCD0001',
            'descricaoTributo' => 'LIC VEICULOS NOVOS/1. REG',
            'valorTaxaLicenciamento' => 452.79,
            'cpfCnpjPrincipal' => 402186670,
            'codigoTributo' => 62,
        ]),
    ]);

    /** @var DebitoVeicularSpMethods $sp */
    $sp = debitoVeicularMethods(DebitoVeicularSpMethods::class);
    $r = $sp->listarDebitosZeroKm([
        'cpfCnpjFilial' => 0,
        'codigoConta' => 404,
        'codigoCanal' => 14,
        'cpfCnpjPrincipal' => 402186670,
        'cpfCnpjDigito' => 19,
        'codigoUf' => 'SP',
        'codigoAgencia' => 3963,
    ]);

    expect($r->descricaoTributo)->toBe('LIC VEICULOS NOVOS/1. REG')
        ->and($r->valorTaxaLicenciamento)->toBe(452.79);
});

it('lista os servicos de taxa do detran SP', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-sp/taxas/lista-servicos/consulta/servico*' => Http::response([
            'codigoRetorno' => 0,
            'quantidadeOcorrencia' => 1,
            'lista' => [['codigoServico' => 1, 'descricaoServico' => 'CNH-CART.NAC.HABILITACAO E REGISTRO']],
        ]),
    ]);

    /** @var DebitoVeicularSpMethods $sp */
    $sp = debitoVeicularMethods(DebitoVeicularSpMethods::class);
    $r = $sp->listarServicosTaxas(['codigoCanal' => 536]);

    expect($r->lista[0]->codigoServico)->toBe(1)
        ->and($r->lista[0]->descricaoServico)->toBe('CNH-CART.NAC.HABILITACAO E REGISTRO');
});

// ---------------------------------------------------------------- MG

it('lista debitos pendentes de MG com o controle de sessao', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-mg/lista-debitos/listaDebitosPendentesMG*' => Http::response([
            'codigoMensagem' => 'LCBR0000',
            'codigoRenavam' => 246304715,
            'codigoPlaca' => 'GLD1623',
            'quantidadeDebitos' => 1,
            'controleSessao' => '03929000000079990600246304715536',
            'debitosListagem' => [[
                'descricaoTributo' => 'IPVA 2021-PARCELA 1',
                'identificadorDebito' => 2505210000004476050,
                'valorTotal' => 205.28,
                'valorJuros' => 62.36,
            ]],
        ]),
    ]);

    /** @var DebitoVeicularMgMethods $mg */
    $mg = debitoVeicularMethods(DebitoVeicularMgMethods::class);
    $r = $mg->listarDebitos([
        'codigoRenavam' => 246304715,
        'codigoConta' => 999,
        'codigoCanal' => 536,
        'codigoAgencia' => 145,
    ]);

    expect($r->controleSessao)->toBe('03929000000079990600246304715536')
        ->and($r->debitosListagem)->toHaveCount(1)
        ->and($r->debitosListagem[0]->identificadorDebito)->toBe(2505210000004476050)
        ->and($r->debitosListagem[0]->valorTotal)->toBe(205.28);
});

it('efetua pagamento em MG devolvendo a autenticacao bancaria', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-mg/efetua-pagamento/efetuaPagamentoMG*' => Http::response([
            'codigoMensagem' => 'LCBR0000',
            'descricaoMensagem' => 'Operação executada com sucesso.',
            'autenticacaoBancaria' => '1164',
            'valorPago' => 205.28,
            'codigoBarras' => '85670000002052800632025052199002463047150211',
        ]),
    ]);

    /** @var DebitoVeicularMgMethods $mg */
    $mg = debitoVeicularMethods(DebitoVeicularMgMethods::class);
    $r = $mg->efetuarPagamento([
        'dataDebito' => '21/05/2025',
        'codigoRenavam' => 246304715,
        'identificadorDebito' => 2505210000004476050,
        'codigoCanal' => 536,
        'meioAutenticacao' => '',
        'controleSessao' => '03929000000079990600246304715536',
        'dispositivoSeguranca' => '',
        'codigoConta' => 999,
        'operacaoLynx' => '',
        'codigoBarras' => '85670000002052800632025052199002463047150211',
        'codigoAgencia' => 145,
        'tipoConta' => 'C',
        'valorPagamento' => 205.28,
    ]);

    expect($r->autenticacaoBancaria)->toBe('1164')
        ->and($r->valorPago)->toBe(205.28);

    Http::assertSent(fn ($req) => str_contains($req->url(), '/v1/debitos-veiculares-mg/efetua-pagamento/efetuaPagamentoMG')
        && $req['controleSessao'] === '03929000000079990600246304715536');
});

it('obtem a guia de MG sem debitar', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-mg/obtem-guia/obtemGuiaMG*' => Http::response([
            'codigoMensagem' => 'LCBR0000',
            'codigoBarras' => '85670000002052800632025052199002463047150211',
            'valorTotal' => 205.28,
            'tipoTributo' => 'IPVA',
        ]),
    ]);

    /** @var DebitoVeicularMgMethods $mg */
    $mg = debitoVeicularMethods(DebitoVeicularMgMethods::class);
    $r = $mg->obterGuia([
        'codigoRenavam' => 246304715,
        'identificadorDebito' => 2505210000004476050,
        'codigoConta' => 999,
        'codigoCanal' => 536,
        'codigoAgencia' => 145,
        'tipoConta' => 'C',
        'controleSessao' => '03929000000079990600246304715536',
    ]);

    expect($r->codigoBarras)->toBe('85670000002052800632025052199002463047150211')
        ->and($r->tipoTributo)->toBe('IPVA');
});

// ---------------------------------------------------------------- PR

it('lista debitos veiculares do PR', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-pr/lista-debitos/listaDebitoVeicularPR*' => Http::response([
            'codigoRetorno' => 0,
            'codigoRenavam' => 323030939,
            'codigoPlaca' => 'HOC1377',
            'devedorPrincipal' => 'SANDRA REGINA SOUZA CARDOSO',
            'nsuBanco' => 28903,
            'lista' => [[
                'nomeTributo' => 'COTA UNICA',
                'codigoTributo' => 451,
                'anoExercicio' => 2023,
                'valorContaTributo' => 1190.0,
            ]],
        ]),
    ]);

    /** @var DebitoVeicularPrMethods $pr */
    $pr = debitoVeicularMethods(DebitoVeicularPrMethods::class);
    $r = $pr->listarDebitos([
        'codigoUF' => 'PR',
        'codigoCanal' => 536,
        'codigoAgencia' => 145,
        'codigoConta' => 999,
        'codigoRenavam' => 323030939,
        'validacaolistaPositiva' => 'N',
    ]);

    expect($r->nsuBanco)->toBe(28903)
        ->and($r->lista[0]->codigoTributo)->toBe(451)
        ->and($r->lista[0]->valorContaTributo)->toBe(1190.0);
});

it('efetua pagamento no PR mandando o codigoFuncao da spec', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-pr/efetua-pagamento/efetuaPagamentoPR*' => Http::response([
            'codigoRetorno' => 0,
            'codigoMensagem' => 'ARCD2782',
            'descricaoMensagem' => 'CONSISTENCIA DOS TRIBUTOS REALIZADAS  COM SUCESSO',
            'nsuBanco' => 28906,
            'valorContaTributo' => 1190.0,
            'codigoAutenticacao' => 0,
        ]),
    ]);

    /** @var DebitoVeicularPrMethods $pr */
    $pr = debitoVeicularMethods(DebitoVeicularPrMethods::class);
    $r = $pr->efetuarPagamento([
        'codigoCanal' => 536,
        'conexao' => 'TERMINAL',
        'sequencialPeriferico' => 'X',
        'identificacaoPeriferico' => 'X',
        'identificacaoLuResposta' => 1,
        'meioAutenticacao' => 'X',
        'codigoFuncao' => 'C',
        'codigoUF' => 'PR',
        'codigoAgencia' => 145,
        'codigoConta' => 999,
        'digitoConta' => 9,
        'codigoRenavam' => 323030939,
        'nsuBanco' => 28906,
        'codigoPlaca' => 'HOC1377',
        'devedorPrincipal' => 'SANDRA REGINA SOUZA CARDOSO',
        'codigoTributo' => 451,
        'descricaoTributo' => '13/12/2024',
        'nomeTributo' => 'COTA UNICA',
        'anoExercicio' => 2023,
        'valorContaTributo' => 1190.0,
        'validacaolistaPositiva' => 'N',
    ]);

    expect($r->nsuBanco)->toBe(28906)
        ->and($r->codigoRetorno)->toBe(0);

    Http::assertSent(fn ($req) => str_contains($req->url(), '/v1/debitos-veiculares-pr/efetua-pagamento/efetuaPagamentoPR')
        && $req['codigoFuncao'] === 'C');
});

// ---------------------------------------------------------------- BA

it('lista debitos da BA por renavam no path com "renavan"', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-ba/detran/lista-debitos/renavan*' => Http::response([
            'codigoRetorno' => 0,
            'codigoRenavam' => 110172930,
            'codigoPlaca' => 'JRQ0135',
            'valorTotal' => 173.4,
            'nsuBanco' => 26480,
            'lista' => [[
                'nomeTributo' => 'TAXA DE LICENCIAMENTO',
                'codigoTributo' => 3,
                'valorContaTributo' => 173.4,
            ]],
        ]),
    ]);

    /** @var DebitoVeicularBaMethods $ba */
    $ba = debitoVeicularMethods(DebitoVeicularBaMethods::class);
    $r = $ba->listarDebitosPorRenavam([
        'codigoRenavam' => 110172930,
        'codigoPagamento' => 401,
        'codigoBanco' => 237,
        'codigoConta' => 999,
        'codigoCanal' => 66,
        'codigoAgencia' => 145,
        'validacaoListaPositiva' => 'N',
    ]);

    expect($r->valorTotal)->toBe(173.4)
        ->and($r->lista[0]->nomeTributo)->toBe('TAXA DE LICENCIAMENTO');

    Http::assertSent(fn ($req) => str_contains($req->url(), '/detran/lista-debitos/renavan'));
});

it('lista debitos da BA por ano e por multa reusando o mesmo DTO', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-ba/detran/lista-debitos/ano*' => Http::response([
            'codigoRenavam' => 154050415,
            'valorIpva' => 710.15,
            'anoExercicio' => 2022,
        ]),
        '*/v1/debitos-veiculares-ba/detran/lista-debitos/multa*' => Http::response([
            'codigoRenavam' => 161616402,
            'numeroMulta' => 436939428,
            'valorTotalMulta' => 201.08,
        ]),
    ]);

    /** @var DebitoVeicularBaMethods $ba */
    $ba = debitoVeicularMethods(DebitoVeicularBaMethods::class);

    $porAno = $ba->listarDebitosPorAno([
        'codigoRenavam' => 154050415,
        'codigoPagamento' => 407,
        'codigoBanco' => 237,
        'codigoConta' => 999,
        'codigoCanal' => 66,
        'anoExercicio' => 2022,
        'codigoAgencia' => 145,
        'validacaoListaPositiva' => 'N',
    ]);

    $porMulta = $ba->listarDebitosPorMulta([
        'codigoRenavam' => 161616402,
        'codigoPagamento' => 411,
        'numeroMulta' => 436939428,
        'codigoBanco' => 237,
        'codigoConta' => 999,
        'codigoCanal' => 66,
        'codigoAgencia' => 145,
        'validacaoListaPositiva' => 'N',
    ]);

    expect($porAno->valorIpva)->toBe(710.15)
        ->and($porAno->anoExercicio)->toBe(2022)
        ->and($porMulta->numeroMulta)->toBe(436939428)
        ->and($porMulta->valorTotalMulta)->toBe(201.08);
});

it('efetua pagamento na BA e devolve os NSUs de rastreio', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-ba/renavam/efetua-pagamento/efetuaPagamentoBA*' => Http::response([
            'codigoRetorno' => 0,
            'codigoMensagem' => 'ARCD2782',
            'descricaoMensagem' => 'CONSISTENCIA DOS TRIBUTOS REALIZADAS  COM SUCESSO',
            'statusPagamento' => 0,
            'nsuOrigem' => 26592,
            'nsuProdeb' => 29174377,
            'valorTotal' => 136.85,
        ]),
    ]);

    /** @var DebitoVeicularBaMethods $ba */
    $ba = debitoVeicularMethods(DebitoVeicularBaMethods::class);
    $r = $ba->efetuarPagamento([
        'codigoFuncao' => 'P',
        'codigoRenavam' => 214059219,
        'codigoPagamento' => 403,
        'nsuOrigem' => 26592,
        'valorTotal' => 136.85,
        'validacaolistaPositiva' => 'N',
    ]);

    expect($r->nsuOrigem)->toBe(26592)
        ->and($r->nsuProdeb)->toBe(29174377)
        ->and($r->statusPagamento)->toBe(0);

    Http::assertSent(fn ($req) => str_contains($req->url(), '/renavam/efetua-pagamento/efetuaPagamentoBA')
        && $req['codigoFuncao'] === 'P');
});

it('consulta comprovante resumido da BA com o campo codigoRenavan da spec', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-ba/renavam/lista-comprovantes/consulta/resumida*' => Http::response([
            'codigoRenavam' => 214059219,
            'codigoPlaca' => 'NTK0617',
            'lista' => [[
                'descricaoTributo' => 'LICENCIAMENTO PARCELADO',
                'dataPagamento' => '29.04.2025',
                'valorContaTributo' => 136.85,
                'nsuBanco' => 26592,
            ]],
        ]),
    ]);

    /** @var DebitoVeicularBaMethods $ba */
    $ba = debitoVeicularMethods(DebitoVeicularBaMethods::class);
    $r = $ba->listarComprovantes([
        'codigoRenavan' => 214059219,
        'codigoBanco' => 237,
        'codigoConta' => 999,
        'anoExercicio' => 2025,
        'codigoUF' => 'BA',
        'codigoAgencia' => 145,
    ]);

    expect($r->lista[0]->nsuBanco)->toBe(26592);

    // O closure roda contra TODAS as requests gravadas (inclusive a do token,
    // que não tem esse campo) — por isso o acesso precisa ser tolerante.
    Http::assertSent(fn ($req) => str_contains($req->url(), '/lista-comprovantes/')
        && ($req['codigoRenavan'] ?? null) === 214059219);
});

// ---------------------------------------------------------------- fachada

it('a fachada distribui o client autenticado por UF', function () {
    Http::fake(debitoVeicularTokens() + [
        '*/v1/debitos-veiculares-pr/lista-comprovantes/listaComprovanteResumidaPr*' => Http::response([
            'codigoRetorno' => 0,
            'codigoRenavam' => 323030939,
            'anoPagamento' => 2023,
            'lista' => [['nomeTributo' => 'COTA 1', 'dataHoraPagamento' => 202308180913300, 'valorContaTributo' => 200.1]],
        ]),
    ]);

    /** @var DebitoVeicularMethods $dv */
    $dv = debitoVeicularMethods(DebitoVeicularMethods::class);

    expect($dv->sp())->toBeInstanceOf(DebitoVeicularSpMethods::class)
        ->and($dv->mg())->toBeInstanceOf(DebitoVeicularMgMethods::class)
        ->and($dv->pr())->toBeInstanceOf(DebitoVeicularPrMethods::class)
        ->and($dv->ba())->toBeInstanceOf(DebitoVeicularBaMethods::class)
        ->and($dv->sp())->toBe($dv->sp());

    $r = $dv->pr()->listarComprovantes([
        'codigoUF' => 'PR',
        'codigoAgencia' => 145,
        'codigoConta' => 999,
        'codigoRenavam' => 323030939,
        'codigoCanal' => 536,
        'anoExercicio' => 2023,
    ]);

    expect($r->lista[0]->dataHoraPagamento)->toBe(202308180913300)
        ->and($r->lista[0]->valorContaTributo)->toBe(200.1);
});
