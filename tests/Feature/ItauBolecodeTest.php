<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Itau\DTO\Response\Bolecode\BolecodeResponse;
use SistemAtc\Banks\Itau\Endpoints\Bolecode\BolecodeMethods;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/**
 * Bolecode Pix — a fiação no connector (Bank::Itau->bolecode()) ainda não
 * existe, então instanciamos a classe de método direto via HttpClientFactory.
 */
function authedBolecode(): FakeBankIntegration
{
    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('emite Bolecode Pix (POST /recebimentos-pix/v1/boletos_pix) desembrulhando data{}', function () {
    Http::fake([
        '*/recebimentos-pix/v1/boletos_pix*' => Http::response([
            'data' => [
                'etapa_processo_boleto' => 'efetivacao',
                'codigo_canal_operacao' => 'API',
                'codigo_operador' => '150000123',
                'beneficiario' => [
                    'id_beneficiario' => '150000123450',
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
                        'endereco' => [
                            'nome_logradouro' => 'Rua endereco, 71',
                            'sigla_UF' => 'PE',
                            'numero_CEP' => '51340540',
                        ],
                        'pagador_eletronico_DDA' => true,
                        'praca_protesto' => false,
                    ],
                    'dados_individuais_boleto' => [
                        [
                            'numero_nosso_numero' => '20000000',
                            'data_vencimento' => '2023-01-14',
                            'valor_titulo' => '00000000000119900',
                            'id_boleto_individual' => 'ID-123',
                            'codigo_barras' => '34191790010104351004791020150008291070026000',
                            'numero_linha_digitavel' => '34191.79001 01043.510047 91020.150008 2 91070026000',
                        ],
                    ],
                    'dados_qrcode' => [
                        'chave' => '12312312000110',
                        'id_location' => 123456,
                        'tipo_cobranca' => 'cob',
                    ],
                ],
            ],
        ]),
    ]);

    $i = authedBolecode();
    $m = new BolecodeMethods(HttpClientFactory::make($i), $i);

    $res = $m->emitir([
        'etapa_processo_boleto' => 'efetivacao',
        'beneficiario' => ['id_beneficiario' => '150000123450'],
        'dado_boleto' => ['descricao_instrumento_cobranca' => 'boleto'],
    ]);

    expect($res)->toBeInstanceOf(BolecodeResponse::class)
        ->and($res->codigoCanalOperacao)->toBe('API')
        ->and($res->beneficiario?->nomeCobranca)->toBe('Soldiers Nutrition')
        ->and($res->beneficiario?->tipoPessoa?->codigoTipoPessoa)->toBe('J')
        ->and($res->dadoBoleto?->codigoTipoVencimento)->toBe(3)
        ->and($res->dadoBoleto?->pagador?->pagadorEletronicoDDA)->toBeTrue()
        ->and($res->dadoBoleto?->pagador?->endereco?->siglaUF)->toBe('PE')
        ->and($res->dadoBoleto?->pagador?->endereco?->numeroCEP)->toBe('51340540')
        ->and($res->dadoBoleto?->dadosIndividuaisBoleto)->toHaveCount(1)
        ->and($res->dadoBoleto?->dadosIndividuaisBoleto[0]->codigoBarras)->toBe('34191790010104351004791020150008291070026000')
        ->and($res->dadoBoleto?->dadosQrcode?->idLocation)->toBe(123456);

    // Envelopa o payload em `data` e carrega os headers obrigatórios do gateway.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/recebimentos-pix/v1/boletos_pix')
        && ($r['data']['etapa_processo_boleto'] ?? null) === 'efetivacao'
        && $r->hasHeader('x-itau-apikey', 'cli')
        && $r->hasHeader('x-itau-correlationID')
        && $r->hasHeader('Authorization', 'Bearer TOK'));
});

it('trata o 202 assincrono (chao, sem data{}) do Bolecode', function () {
    Http::fake([
        '*/recebimentos-pix/v1/boletos_pix*' => Http::response([
            'codigo' => '202',
            'mensagem' => 'Operação em andamento, consulte seu bolecode em instantes',
        ], 202),
    ]);

    $i = authedBolecode();
    $m = new BolecodeMethods(HttpClientFactory::make($i), $i);

    $res = $m->emitir(['etapa_processo_boleto' => 'efetivacao']);

    expect($res)->toBeInstanceOf(BolecodeResponse::class)
        ->and($res->codigo)->toBe('202')
        ->and($res->mensagem)->toContain('Operação em andamento');
});
