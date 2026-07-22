<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Itau\Endpoints\PixAutomatico\CobrancaRecorrenteMethods;
use SistemAtc\Banks\Itau\Endpoints\PixAutomatico\QrCodeMethods;
use SistemAtc\Banks\Itau\Endpoints\PixAutomatico\RecorrenciaMethods;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\Cobranca;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\CobrancaRecorrente;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\CobrancaRecorrenteList;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\Recorrencia;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\RecorrenciaList;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\SolicitacaoRecorrencia;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function pixAutoAuthed(): FakeBankIntegration
{
    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('cria recorrencia (POST /rec) hidratando vinculo/valor/calendario', function () {
    Http::fake([
        '*/rec' => Http::response([
            'idRec' => 'RN1234567820240115abcdefghijk',
            'status' => 'CRIADA',
            'politicaRetentativa' => 'NAO_PERMITE',
            'valor' => ['valorRec' => '35.00'],
            'vinculo' => [
                'contrato' => '63100862',
                'devedor' => ['cpf' => '45164632481', 'nome' => 'Fulano de Tal'],
                'objeto' => 'Serviço de Streamming de Música.',
            ],
            'calendario' => ['dataInicial' => '2024-04-01', 'dataFinal' => '2025-04-01', 'periodicidade' => 'MENSAL'],
            'loc' => ['id' => 108, 'location' => 'pix.example.com/qr/v2/rec/2353c790', 'idRec' => 'RN1234567820240115abcdefghijk'],
            'ativacao' => ['dadosJornada' => ['tipoJornada' => 'JORNADA_3', 'txid' => '33beb661beda44a8928fef47dbeb2dc5']],
            'atualizacao' => [['data' => '2023-12-19T12:28:05.230Z', 'nome' => 'CRIADA']],
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new RecorrenciaMethods(HttpClientFactory::make($i), $i);
    $res = $m->criar(['vinculo' => ['contrato' => '63100862']]);

    expect($res)->toBeInstanceOf(Recorrencia::class)
        ->and($res->idRec)->toBe('RN1234567820240115abcdefghijk')
        ->and($res->status)->toBe('CRIADA')
        ->and($res->valor?->valorRec)->toBe('35.00')
        ->and($res->vinculo?->devedor?->nome)->toBe('Fulano de Tal')
        ->and($res->calendario?->periodicidade)->toBe('MENSAL')
        ->and($res->loc?->id)->toBe(108)
        ->and($res->ativacao?->dadosJornada?->txid)->toBe('33beb661beda44a8928fef47dbeb2dc5')
        ->and($res->atualizacao)->toHaveCount(1)
        ->and($res->atualizacao[0]->nome)->toBe('CRIADA');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/rec')
        && $r->method() === 'POST'
        && $r->hasHeader('x-itau-apikey', 'cli')
        && $r->hasHeader('x-itau-correlationID')
        && $r->hasHeader('Authorization', 'Bearer TOK'));
});

it('consulta recorrencia por idRec (GET /rec/{idRec})', function () {
    Http::fake([
        '*/rec/RN123*' => Http::response([
            'idRec' => 'RN123',
            'status' => 'APROVADA',
            'dadosQR' => ['jornada' => 'JORNADA_2', 'pixCopiaECola' => '00020126180014br.gov.bcb.pix'],
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new RecorrenciaMethods(HttpClientFactory::make($i), $i);
    $res = $m->consultar('RN123');

    expect($res)->toBeInstanceOf(Recorrencia::class)
        ->and($res->status)->toBe('APROVADA')
        ->and($res->dadosQR?->pixCopiaECola)->toStartWith('000201');
});

it('lista recorrencias (GET /rec) hidratando parametros.paginacao e recs[]', function () {
    Http::fake([
        '*/rec*' => Http::response([
            'parametros' => [
                'inicio' => '2024-04-01T00:00:00Z',
                'fim' => '2024-04-01T23:59:59Z',
                'paginacao' => ['paginaAtual' => 0, 'itensPorPagina' => 100, 'quantidadeDePaginas' => 1, 'quantidadeTotalDeItens' => 1],
            ],
            'recs' => [
                ['idRec' => 'RN1', 'status' => 'APROVADA', 'valor' => ['valorRec' => '300.00']],
            ],
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new RecorrenciaMethods(HttpClientFactory::make($i), $i);
    $res = $m->listar(['inicio' => '2024-04-01T00:00:00Z', 'fim' => '2024-04-01T23:59:59Z']);

    expect($res)->toBeInstanceOf(RecorrenciaList::class)
        ->and($res->parametros?->paginacao?->quantidadeTotalDeItens)->toBe(1)
        ->and($res->recs)->toHaveCount(1)
        ->and($res->recs[0]->idRec)->toBe('RN1');
});

it('cria solicitacao de recorrencia (POST /solicrec) com recPayload aninhado', function () {
    Http::fake([
        '*/solicrec' => Http::response([
            'idSolicRec' => 'SC876456782024021577825445312',
            'idRec' => 'RN123456782024011577825445612',
            'status' => 'CRIADA',
            'destinatario' => ['agencia' => '2569', 'conta' => '550689', 'cpf' => '15231470190', 'ispbParticipante' => '91193552'],
            'recPayload' => ['idRec' => 'RN123456782024011577825445612', 'valor' => ['valorRec' => '1200.09']],
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new RecorrenciaMethods(HttpClientFactory::make($i), $i);
    $res = $m->criarSolicitacao(['idRec' => 'RN123456782024011577825445612']);

    expect($res)->toBeInstanceOf(SolicitacaoRecorrencia::class)
        ->and($res->idSolicRec)->toBe('SC876456782024021577825445312')
        ->and($res->destinatario?->conta)->toBe('550689')
        ->and($res->recPayload?->valor?->valorRec)->toBe('1200.09');
});

it('cria cobranca recorrente com txid (PUT /cobr/{txid}) hidratando devedor de endereco', function () {
    Http::fake([
        '*/cobr/*' => Http::response([
            'idRec' => 'RR1234567820240115abcdefghijk',
            'txid' => '3136957d93134f2184b369e8f1c0729d',
            'infoAdicional' => 'Serviços de Streamming.',
            'status' => 'CRIADA',
            'ajusteDiaUtil' => true,
            'valor' => ['original' => '106.07'],
            'calendario' => ['criacao' => '2024-04-01', 'dataDeVencimento' => '2024-04-15'],
            'devedor' => ['cep' => '89256-140', 'cidade' => 'Uberlândia', 'email' => 's@mail.com', 'uf' => 'MG'],
            'recebedor' => ['agencia' => '9708', 'conta' => '012682', 'tipoConta' => 'CORRENTE'],
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new CobrancaRecorrenteMethods(HttpClientFactory::make($i), $i);
    $res = $m->criarComTxid('3136957d93134f2184b369e8f1c0729d', ['idRec' => 'RR1234567820240115abcdefghijk']);

    expect($res)->toBeInstanceOf(CobrancaRecorrente::class)
        ->and($res->txid)->toBe('3136957d93134f2184b369e8f1c0729d')
        ->and($res->ajusteDiaUtil)->toBeTrue()
        ->and($res->valor?->original)->toBe('106.07')
        ->and($res->devedor?->uf)->toBe('MG')
        ->and($res->recebedor?->tipoConta)->toBe('CORRENTE');

    Http::assertSent(fn ($r) => $r->method() === 'PUT' && str_contains($r->url(), '/cobr/'));
});

it('lista cobrancas recorrentes (GET /cobr) na chave cobsr com tentativas[]', function () {
    Http::fake([
        '*/cobr*' => Http::response([
            'parametros' => ['inicio' => '2024-04-01T00:00:00Z', 'fim' => '2024-12-01T23:59:59Z'],
            'cobsr' => [
                [
                    'idRec' => 'RR123',
                    'txid' => '7f733863543b4a16b516d839bd4bc34e',
                    'status' => 'ATIVA',
                    'tentativas' => [
                        ['dataLiquidacao' => '2024-06-20', 'tipo' => 'AGND', 'status' => 'AGENDADA',
                            'atualizacao' => [['data' => '2024-05-21T10:40:16.730Z', 'status' => 'SOLICITADA']]],
                    ],
                ],
            ],
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new CobrancaRecorrenteMethods(HttpClientFactory::make($i), $i);
    $res = $m->listar(['inicio' => '2024-04-01T00:00:00Z', 'fim' => '2024-12-01T23:59:59Z', 'idRec' => 'RR123']);

    expect($res)->toBeInstanceOf(CobrancaRecorrenteList::class)
        ->and($res->cobsr)->toHaveCount(1)
        ->and($res->cobsr[0]->txid)->toBe('7f733863543b4a16b516d839bd4bc34e')
        ->and($res->cobsr[0]->tentativas[0]->status)->toBe('AGENDADA')
        ->and($res->cobsr[0]->tentativas[0]->atualizacao[0]->status)->toBe('SOLICITADA');
});

it('solicita retentativa (POST /cobr/{txid}/retentativa/{data})', function () {
    Http::fake([
        '*/cobr/*/retentativa/*' => Http::response([
            'txid' => '7f733863543b4a16b516d839bd4bc34e',
            'status' => 'ATIVA',
            'politicaRetentativa' => 'PERMITE_3R_7D',
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new CobrancaRecorrenteMethods(HttpClientFactory::make($i), $i);
    $res = $m->solicitarRetentativa('7f733863543b4a16b516d839bd4bc34e', '2024-06-24');

    expect($res)->toBeInstanceOf(CobrancaRecorrente::class)
        ->and($res->politicaRetentativa)->toBe('PERMITE_3R_7D');

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && str_contains($r->url(), '/retentativa/2024-06-24'));
});

it('emite QR Code Pix Automatico (POST /cobrancas) no host dedicado', function () {
    Http::fake([
        '*/qrcode-pix-automatico/v1/cobrancas*' => Http::response([
            'cobrancaId' => 'abc123',
            'txid' => 'txid123',
            'status' => 'ATIVA',
            'valor' => ['original' => '50.00'],
            'pixCopiaECola' => '00020101021226',
        ]),
    ]);

    $i = pixAutoAuthed();
    $m = new QrCodeMethods(HttpClientFactory::make($i), $i);
    $res = $m->criar(['valor' => ['original' => '50.00']]);

    expect($res)->toBeInstanceOf(Cobranca::class)
        ->and($res->cobrancaId)->toBe('abc123')
        ->and($res->valor?->original)->toBe('50.00')
        ->and($res->pixCopiaECola)->toStartWith('000201');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/qrcode-pix-automatico/v1/cobrancas'));
});
