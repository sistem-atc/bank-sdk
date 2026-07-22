<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias\SolicitarTransferenciaResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias\TransferenciaResponse;
use SistemAtc\Banks\Bradesco\Endpoints\PixTransferencias\PixTransferenciasMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function authedPixTransferencias(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function pixTransferenciasMethods(): PixTransferenciasMethods
{
    $i = authedPixTransferencias();

    return new PixTransferenciasMethods(
        HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX),
        $i,
    );
}

/** Tokens das DUAS famílias — o factory autentica antes da chamada. */
function fakePixTransferenciasTokens(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ];
}

it('solicita transferencia Pix via POST /v1/spi/solicitar-transferencia', function () {
    Http::fake(fakePixTransferenciasTokens() + [
        '*/v1/spi/solicitar-transferencia*' => Http::response([
            'pagador' => [
                'cpfCnpj' => '97803114002740',
                'agencia' => '3877',
                'conta' => '20220',
                'tipoConta' => 'CONTA_CORRENTE',
            ],
            'recebedor' => [
                'cpfCnpj' => '97803114002740',
                'tipoChave' => 'EVP',
                'chavePix' => 'b6295ee1-f054-47d1-9e90-ee57b74f60d9',
                'nomeFavorecido' => 'Gustavo Santos',
            ],
            'valor' => '200.00',
            'e2e' => 'E60746948202204271435L3877xxXxxX',
            'idTransacao' => 'txIdExemploTransacaoTeste01',
            'descricao' => 'Pagamento de cobranca',
            'dataCriacao' => '2022-06-06T17:54:08.083Z',
            'status' => 'CONCLUIDO',
            'motivo' => 'Transação efetuada com sucesso',
        ]),
    ]);

    $res = pixTransferenciasMethods()->solicitar([
        'idtransacao' => 'txIdExemploTransacaoTeste01',
        'valor' => '200.00',
        'pagador' => [
            'agencia' => '3877',
            'conta' => '20220',
            'cpfCnpj' => '97803114002740',
            'tipoConta' => 'CONTA_CORRENTE',
        ],
        'recebedor' => [
            'nomeFavorecido' => 'Gustavo Santos',
            'tipoChave' => 'EVP',
            'chavePix' => 'b6295ee1-f054-47d1-9e90-ee57b74f60d9',
            'cpfCnpj' => '97803114002740',
        ],
        'descricao' => 'Pagamento de cobranca',
    ]);

    expect($res)->toBeInstanceOf(SolicitarTransferenciaResponse::class)
        ->and($res->status)->toBe('CONCLUIDO')
        ->and($res->e2e)->toBe('E60746948202204271435L3877xxXxxX')
        ->and($res->idTransacao)->toBe('txIdExemploTransacaoTeste01')
        ->and($res->valor)->toBe('200.00')
        ->and($res->pagador?->agencia)->toBe('3877')
        ->and($res->recebedor?->nomeFavorecido)->toBe('Gustavo Santos');

    // Host da família PIX + verbo/path exatos da spec, e o idtransacao (TXID,
    // identificador de idempotência) viajando no corpo.
    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && str_contains($r->url(), 'qrpix.bradesco.com.br')
        && str_contains($r->url(), '/v1/spi/solicitar-transferencia')
        && $r['idtransacao'] === 'txIdExemploTransacaoTeste01');
});

it('trata 202 (transacao rejeitada) devolvendo status REJEITADO e codigoMotivo', function () {
    Http::fake(fakePixTransferenciasTokens() + [
        '*/v1/spi/solicitar-transferencia*' => Http::response([
            'idTransacao' => 'txIdExemploTransacaoTeste01',
            'e2e' => 'E60746948202204271435L3877xxXxxX',
            'valor' => '200.00',
            'status' => 'REJEITADO',
            'motivo' => 'Não foi possivel realizar a transação',
            'codigoMotivo' => '3333',
            'dataCriacao' => '2022-06-06T17:54:08.083Z',
        ], 202),
    ]);

    $res = pixTransferenciasMethods()->solicitar(['idtransacao' => 'txIdExemploTransacaoTeste01']);

    expect($res->status)->toBe('REJEITADO')
        ->and($res->codigoMotivo)->toBe('3333')
        ->and($res->motivo)->toBe('Não foi possivel realizar a transação');
});

it('consulta transferencia por id-transacao (GET /v1/spi/consulta/transferencia/{id})', function () {
    Http::fake(fakePixTransferenciasTokens() + [
        '*/v1/spi/consulta/transferencia/*' => Http::response([
            'pagador' => [
                'cpfCnpj' => '97803114002740',
                'agencia' => '3877',
                'conta' => '20220',
                'tipoConta' => 'CONTA_CORRENTE',
            ],
            'recebedor' => [
                'cpfCnpj' => '97803114002740',
                'tipoChave' => 'AGENCIACONTA',
                'tipoConta' => 'CONTA_CORRENTE',
                'ispb' => '00000000',
                'agencia' => '3877',
                'conta' => '20220',
                'banco' => '237',
                'nomeFavorecido' => 'Gustavo Santos',
            ],
            'valor' => '200.00',
            'e2e' => 'E60746948202204271435L3877xxXxxX',
            'idTransacao' => 'txIdExemploTransacaoTeste01',
            'dataCriacao' => '2022-06-06T17:54:08.083Z',
            'dataEfetivacao' => '2022-06-06T17:54:09.083Z',
            'status' => 'CONCLUIDO',
            'motivo' => 'Transação efetuada com sucesso',
        ]),
    ]);

    $res = pixTransferenciasMethods()->consultar('txIdExemploTransacaoTeste01');

    expect($res)->toBeInstanceOf(TransferenciaResponse::class)
        ->and($res->idTransacao)->toBe('txIdExemploTransacaoTeste01')
        ->and($res->status)->toBe('CONCLUIDO')
        ->and($res->dataEfetivacao)->toBe('2022-06-06T17:54:09.083Z')
        ->and($res->pagador?->conta)->toBe('20220')
        ->and($res->recebedor?->banco)->toBe('237');

    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_contains($r->url(), '/v1/spi/consulta/transferencia/txIdExemploTransacaoTeste01'));
});

it('consulta transferencia por id-transacao + e2e', function () {
    Http::fake(fakePixTransferenciasTokens() + [
        '*/v1/spi/consulta/transferencia/*' => Http::response([
            'idTransacao' => 'txIdExemploTransacaoTeste01',
            'e2e' => 'E60746948202204271435L3877xxXxxX',
            'valor' => '200.00',
            'status' => 'EM_PROCESSAMENTO',
        ]),
    ]);

    $res = pixTransferenciasMethods()->consultarPorE2e(
        'txIdExemploTransacaoTeste01',
        'E60746948202204271435L3877xxXxxX',
    );

    expect($res->status)->toBe('EM_PROCESSAMENTO');

    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_contains(
            $r->url(),
            '/v1/spi/consulta/transferencia/txIdExemploTransacaoTeste01/E60746948202204271435L3877xxXxxX'
        ));
});
