<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\BoletoConsultaResponse;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\BoletoEmissaoResponse;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\MovimentacaoExtratoResponse;
use SistemAtc\Banks\Itau\Endpoints\Boletos\BoletosConsultaMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\BoletosExtratoMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\BoletosInstrucaoMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\BoletosMethods;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/**
 * Boletos Cobrança — a fiação no connector (Bank::Itau->boletos()) ainda não
 * existe, então instanciamos as classes de método direto via HttpClientFactory.
 */
function authedBoletos(): FakeBankIntegration
{
    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('emite boleto (POST /cash_management/v2/boletos) desembrulhando data{}', function () {
    Http::fake([
        '*/cash_management/v2/boletos' => Http::response([
            'data' => [
                'etapa_processo_boleto' => 'efetivacao',
                'codigo_canal_operacao' => 'API',
                'codigo_operador' => '889911348',
                'beneficiario' => [
                    'id_beneficiario' => '150000052061',
                    'nome_cobranca' => 'Soldiers Nutrition',
                    'tipo_pessoa' => [
                        'codigo_tipo_pessoa' => 'J',
                        'numero_cadastro_nacional_pessoa_juridica' => '12312312000110',
                    ],
                ],
                'dado_boleto' => [
                    'descricao_instrumento_cobranca' => 'boleto',
                    'tipo_boleto' => 'a vista',
                    'codigo_carteira' => '109',
                    'codigo_tipo_vencimento' => 3,
                    'pagador' => [
                        'pessoa' => [
                            'nome_pessoa' => 'Pessoa teste',
                            'tipo_pessoa' => ['codigo_tipo_pessoa' => 'F', 'numero_cadastro_pessoa_fisica' => '12345678910'],
                        ],
                        'endereco' => ['sigla_UF' => 'SP', 'numero_CEP' => '04131020'],
                        'pagador_eletronico_DDA' => false,
                        'praca_protesto' => true,
                    ],
                    'dados_individuais_boleto' => [
                        [
                            'id_boleto_individual' => 'a6e48c70-5bc4-492e-b7d8-eb65c056a1bb',
                            'numero_nosso_numero' => '00001046',
                            'dac_titulo' => '9',
                            'data_vencimento' => '2021-06-01',
                            'valor_titulo' => '00000000000010001',
                            'codigo_barras' => '34194863800000100011570000104691500052061000',
                            'numero_linha_digitavel' => '34191570070010469150600520610007486380000010001',
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $i = authedBoletos();
    $m = new BoletosMethods(HttpClientFactory::make($i), $i);

    $res = $m->emitir([
        'etapa_processo_boleto' => 'efetivacao',
        'beneficiario' => ['id_beneficiario' => '150000052061'],
        'dado_boleto' => ['descricao_instrumento_cobranca' => 'boleto'],
    ]);

    expect($res)->toBeInstanceOf(BoletoEmissaoResponse::class)
        ->and($res->codigoCanalOperacao)->toBe('API')
        ->and($res->beneficiario?->nomeCobranca)->toBe('Soldiers Nutrition')
        ->and($res->dadoBoleto?->codigoTipoVencimento)->toBe(3)
        ->and($res->dadoBoleto?->pagador?->pagadorEletronicoDDA)->toBeFalse()
        ->and($res->dadoBoleto?->pagador?->endereco?->siglaUF)->toBe('SP')
        ->and($res->dadoBoleto?->pagador?->endereco?->numeroCEP)->toBe('04131020')
        ->and($res->dadoBoleto?->dadosIndividuaisBoleto)->toHaveCount(1)
        ->and($res->dadoBoleto?->dadosIndividuaisBoleto[0]->numeroLinhaDigitavel)
            ->toBe('34191570070010469150600520610007486380000010001');

    // Envelopa o payload em `data` e carrega os headers obrigatórios do gateway.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/cash_management/v2/boletos')
        && ($r['data']['etapa_processo_boleto'] ?? null) === 'efetivacao'
        && $r->hasHeader('x-itau-apikey', 'cli')
        && $r->hasHeader('Authorization', 'Bearer TOK'));
});

it('simula emissao forcando etapa_processo_boleto=validacao', function () {
    Http::fake(['*/cash_management/v2/boletos' => Http::response(['data' => ['etapa_processo_boleto' => 'validacao']])]);

    $i = authedBoletos();
    $m = new BoletosMethods(HttpClientFactory::make($i), $i);

    $res = $m->simular(['beneficiario' => ['id_beneficiario' => '150000052061']]);

    expect($res)->toBeInstanceOf(BoletoEmissaoResponse::class)
        ->and($res->etapaProcessoBoleto)->toBe('validacao');

    Http::assertSent(fn ($r) => ($r['data']['etapa_processo_boleto'] ?? null) === 'validacao');
});

it('baixa e altera valor nominal (PATCH .../{id}/baixa e /valor_nominal, 204)', function () {
    Http::fake(['*/cash_management/v2/boletos/*' => Http::response([], 204)]);

    $i = authedBoletos();
    $m = new BoletosInstrucaoMethods(HttpClientFactory::make($i), $i);

    $m->baixar('15000005206110900123522');
    $m->alterarValorNominal('15000005201215600000074', '500.00');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boletos/15000005206110900123522/baixa')
        && $r->method() === 'PATCH');
    Http::assertSent(fn ($r) => str_contains($r->url(), '/boletos/15000005201215600000074/valor_nominal')
        && ($r['valor_titulo'] ?? null) === '500.00');
});

it('consulta detalhe do boleto (GET /boletoscash/v2/boletos) com data[] + page', function () {
    Http::fake([
        '*/boletoscash/v2/boletos*' => Http::response([
            'data' => [
                [
                    'id_boleto' => 'abc25217-c383-4655-b44f-1087a2d2b80f',
                    'beneficiario' => ['id_beneficiario' => '150000011091', 'nome_cobranca' => 'Beneficiario Teste'],
                    'dado_boleto' => [
                        'descricao_instrumento_cobranca' => 'BoleCode',
                        'codigo_carteira' => '157',
                        'dados_individuais_boleto' => [
                            [
                                'situacao_geral_boleto' => 'Em Aberto',
                                'status_vencimento' => 'A vencer',
                                'numero_nosso_numero' => '47001',
                                'valor_titulo' => '100.00',
                                'dac_titulo' => 2,
                            ],
                        ],
                    ],
                    'acoes_permitidas' => ['emitir_segunda_via' => true],
                ],
            ],
            'page' => ['page' => 0, 'total_elements' => 1, 'page_size' => 20],
        ]),
    ]);

    $i = authedBoletos();
    $m = new BoletosConsultaMethods(HttpClientFactory::make($i), $i);

    $res = $m->consultarDetalhe('150000011091', '157', '00047001');

    expect($res)->toBeInstanceOf(BoletoConsultaResponse::class)
        ->and($res->data)->toHaveCount(1)
        ->and($res->data[0]->idBoleto)->toBe('abc25217-c383-4655-b44f-1087a2d2b80f')
        ->and($res->data[0]->dadoBoleto?->codigoCarteira)->toBe('157')
        ->and($res->data[0]->dadoBoleto?->dadosIndividuaisBoleto[0]->situacaoGeralBoleto)->toBe('Em Aberto')
        ->and($res->data[0]->dadoBoleto?->dadosIndividuaisBoleto[0]->dacTitulo)->toBe('2')
        ->and($res->page['total_elements'] ?? null)->toBe(1);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boletoscash/v2/boletos')
        && str_contains($r->url(), 'id_beneficiario=150000011091')
        && str_contains($r->url(), 'view=specific'));
});

it('lista extrato detalhado de movimentacoes (GET /extrato/v1/francesas/{id}/movimentacoes)', function () {
    Http::fake([
        '*/extrato/v1/francesas/*/movimentacoes*' => Http::response([
            'data' => [
                [
                    'agencia' => 1234,
                    'conta' => 12345678,
                    'dataMovimentacao' => '2020-03-12',
                    'codigoStatus' => 'E',
                    'tipoMovimentacao' => 'entradas',
                    'nossoNumero' => 85022822,
                    'seuNumero' => '12346A-B',
                    'valorTitulo' => '100.00',
                    'indicadorRateioCredito' => false,
                ],
            ],
            'pagination' => ['page' => 0],
        ]),
    ]);

    $i = authedBoletos();
    $m = new BoletosExtratoMethods(HttpClientFactory::make($i), $i);

    $res = $m->movimentacoes('150000015605', ['data' => '2021-11-03', 'nossoNumero' => '85022822', 'numeroCarteira' => '109']);

    expect($res)->toBeInstanceOf(MovimentacaoExtratoResponse::class)
        ->and($res->data)->toHaveCount(1)
        ->and($res->data[0]->codigoStatus)->toBe('E')
        ->and($res->data[0]->tipoMovimentacao)->toBe('entradas')
        ->and($res->data[0]->nossoNumero)->toBe(85022822)
        ->and($res->data[0]->valorTitulo)->toBe('100.00');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/extrato/v1/francesas/150000015605/movimentacoes')
        && str_contains($r->url(), 'data=2021-11-03'));
});
