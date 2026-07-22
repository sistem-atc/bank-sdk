<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\NotificacaoBoleto;
use SistemAtc\Banks\Itau\Enums\TipoNotificacaoBoleto;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function itauWebhookIntegration(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    $i = new FakeBankIntegration(sandbox: false);
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('cadastra o webhook de boletos no host proprio da v3', function () {
    Http::fake([
        '*/boletos/v3/notificacoes_boletos*' => Http::response([
            'id_notificacao_boleto' => 'b78cc2ff-cec3-4b15-be24-0073cb233266',
            'id_beneficiario' => '150000154711',
            'webhook_url' => 'https://bunker.example.com/api/webhooks/itau-boletos',
            'webhook_oauth_url' => 'https://bunker.example.com/oauth/token',
            'webhook_oauth_scope' => 'boletos-notificacao',
            'valor_minimo' => 1575.15,
            'data_criacao' => '2026-07-22 09:31:22',
            'tipo_notificacao' => 'BAIXA_EFETIVA',
        ]),
    ]);

    $res = Bank::Itau->boletos(itauWebhookIntegration())->notificacoes()->cadastrar([
        'id_beneficiario' => '150000154711',
        'webhook_url' => 'https://bunker.example.com/api/webhooks/itau-boletos',
        'webhook_client_id' => '821a721a-8873-4b6a-af68-da4a48cb39f6',
        'webhook_client_secret' => 'f57850b5-02b0-4c6a-8154-5b94541204ed',
        'webhook_oauth_url' => 'https://bunker.example.com/oauth/token',
        'webhook_oauth_scope' => 'boletos-notificacao',
        'valor_minimo' => 1575.15,
        'tipos_notificacoes' => ['BAIXA_EFETIVA', 'BAIXA_OPERACIONAL'],
    ]);

    expect($res)->toBeInstanceOf(NotificacaoBoleto::class)
        ->and($res->idNotificacaoBoleto)->toBe('b78cc2ff-cec3-4b15-be24-0073cb233266')
        ->and($res->tipoNotificacao)->toBe(TipoNotificacaoBoleto::BAIXA_EFETIVA)
        ->and($res->valorMinimo)->toBe(1575.15);

    // Host da v3 é distinto do host de emissão de boleto.
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://boletos.cloud.itau.com.br/boletos/v3/notificacoes_boletos')
        && $r->method() === 'POST'
        && $r->hasHeader('x-itau-apikey')
        && $r->hasHeader('x-itau-correlationID'));
});

it('consulta os cadastros do beneficiario filtrando por tipo', function () {
    Http::fake([
        '*/notificacoes_boletos*' => Http::response([
            ['id_notificacao_boleto' => 'a1', 'tipo_notificacao' => 'BAIXA_OPERACIONAL', 'id_beneficiario' => '150000154711'],
            ['id_notificacao_boleto' => 'a2', 'tipo_notificacao' => 'BAIXA_EFETIVA', 'id_beneficiario' => '150000154711'],
        ]),
    ]);

    $list = Bank::Itau->boletos(itauWebhookIntegration())
        ->notificacoes()
        ->consultar('150000154711', TipoNotificacaoBoleto::BAIXA_OPERACIONAL);

    // Lista crua (sem envelope `data`) é normalizada pelo método.
    expect($list->data)->toHaveCount(2)
        ->and($list->data[0]->tipoNotificacao)->toBe(TipoNotificacaoBoleto::BAIXA_OPERACIONAL);

    Http::assertSent(fn ($r) => str_contains($r->url(), 'id_beneficiario=150000154711')
        && str_contains($r->url(), 'tipo_notificacao=BAIXA_OPERACIONAL'));
});

it('exclui um cadastro de notificacao', function () {
    Http::fake(['*/notificacoes_boletos/*' => Http::response([], 204)]);

    Bank::Itau->boletos(itauWebhookIntegration())->notificacoes()->excluir('b78cc2ff');

    Http::assertSent(fn ($r) => $r->method() === 'DELETE'
        && str_ends_with($r->url(), '/notificacoes_boletos/b78cc2ff'));
});
