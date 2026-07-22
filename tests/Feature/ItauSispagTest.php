<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Itau\DTO\Response\Sispag\PagamentosSispagList;
use SistemAtc\Banks\Itau\DTO\Response\Sispag\TransferenciaResponse;
use SistemAtc\Banks\Support\ClientCertificate;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function itauAuthed(): FakeBankIntegration
{
    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('inclui Pix via SISPAG (POST /sispag/v1/transferencias) com headers obrigatorios', function () {
    Http::fake([
        '*/sispag/v1/transferencias*' => Http::response([
            'status_pagamento' => 'Sucesso',
            'cod_pagamento' => '916BD0D6-CDC9-41E8-B2A5-19FF6BC0F823',
            'tipo_pagamento' => 'Transferência Pix Itaú',
            'valor_pagamento' => '1260.00',
            'recebedor' => ['nome' => 'Maria PIX', 'identificacao_chave' => 'maria_pix@gmail.com'],
        ]),
    ]);

    $res = Bank::Itau->pix(itauAuthed())->pagar([
        'valor_pagamento' => '1260.00',
        'data_pagamento' => '2026-07-22',
        'chave' => 'maria_pix@gmail.com',
    ]);

    expect($res)->toBeInstanceOf(TransferenciaResponse::class)
        ->and($res->statusPagamento)->toBe('Sucesso')
        ->and($res->codPagamento)->toBe('916BD0D6-CDC9-41E8-B2A5-19FF6BC0F823')
        ->and($res->recebedor?->nome)->toBe('Maria PIX');

    // Headers obrigatórios do gateway Itaú em toda chamada.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/sispag/v1/transferencias')
        && $r->hasHeader('x-itau-apikey', 'cli')
        && $r->hasHeader('x-itau-correlationID')
        && $r->hasHeader('Authorization', 'Bearer TOK'));
});

it('lista pagamentos SISPAG desembrulhando data{itens,total,pagination}', function () {
    Http::fake([
        '*/sispag/v1/pagamentos_sispag*' => Http::response([
            'data' => [
                'itens' => [
                    ['id_pagamento' => 'a1', 'status' => 'Efetuado', 'valor_pagamento' => '11260.00'],
                    ['id_pagamento' => 'a2', 'status' => 'Não Efetuado'],
                ],
                'total' => '54750523.19',
                'page' => 0,
                'page_size' => 50,
            ],
        ]),
    ]);

    $list = Bank::Itau->payments(itauAuthed())->listar(['conta_operacao' => '00062573']);

    expect($list)->toBeInstanceOf(PagamentosSispagList::class)
        ->and($list->itens)->toHaveCount(2)
        ->and($list->itens[0]->status)->toBe('Efetuado')
        ->and($list->total)->toBe('54750523.19');
});

it('usa private_key_jwt (client_assertion) quando a integracao pede', function () {
    // Chave RSA de teste, gerada em memória (não é segredo real).
    $res = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($res, $pem);

    $integration = new FakeBankIntegration(
        settings: ['auth_method' => 'private_key_jwt', 'jwt_key_pem' => $pem],
    );

    Http::fake([
        '*/sandbox/api/oauth/token' => Http::response(['access_token' => 'JWT_TOK', 'expires_in' => 300]),
    ]);

    $token = Bank::Itau->auth($integration);

    expect($token->accessToken)->toBe('JWT_TOK');

    // Mandou client_assertion (não client_secret) no grant.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/oauth/token')
        && ($r['client_assertion_type'] ?? null) === 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer'
        && ! empty($r['client_assertion'])
        && ! isset($r['client_secret']));
});
