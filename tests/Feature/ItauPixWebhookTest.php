<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use SistemAtc\Banks\Webhooks\BankWebhookReceived;
use SistemAtc\Banks\Webhooks\Receivers\ItauWebhookReceiver;
use SistemAtc\Banks\Webhooks\Receivers\RequiresClientCertificate;
use SistemAtc\Banks\Webhooks\Receivers\WebhookReceivers;
use SistemAtc\Banks\Webhooks\WebhookUrl;

/** @return ArrayObject<int, BankWebhookReceived> */
function avisosItau(): ArrayObject
{
    $recebidos = new ArrayObject();
    Event::listen(BankWebhookReceived::class, fn (BankWebhookReceived $e) => $recebidos->append($e));

    return $recebidos;
}

beforeEach(fn () => config(['app.url' => 'https://erp.exemplo.com.br']));

it('o SDK registra a rota de webhook do Itaú sozinho', function () {
    expect(Route::has('banks.webhooks.itau'))->toBeTrue()
        ->and(WebhookReceivers::for('itau'))->toBeInstanceOf(ItauWebhookReceiver::class)
        ->and(WebhookReceivers::for('itau'))->toBeInstanceOf(RequiresClientCertificate::class);
});

it('recebe o Pix recebido com o /pix que o Itaú acrescenta à URL', function () {
    $avisos = avisosItau();
    $corpo = ['pix' => [['endToEndId' => 'E60701190', 'txid' => 'LOJAINTERNA7T1', 'valor' => '16.36', 'horario' => '2026-11-17T10:00:00Z']]];

    $this->postJson('/api/webhooks/banks/itau/42/pix/pix', $corpo, ['X-SSL-Client-Verify' => 'SUCCESS'])
        ->assertOk();

    expect($avisos)->toHaveCount(1)
        ->and($avisos[0]->bank)->toBe('itau')
        ->and($avisos[0]->integrationId)->toBe('42')
        ->and($avisos[0]->tipo)->toBe('pix')
        ->and($avisos[0]->payload)->toBe($corpo);
});

it('recusa aviso sem o certificado do Itaú conferido pelo proxy', function () {
    $avisos = avisosItau();

    $this->postJson('/api/webhooks/banks/itau/42/pix', ['pix' => []])->assertStatus(401);
    $this->postJson('/api/webhooks/banks/itau/42/pix', ['pix' => []], ['X-SSL-Client-Verify' => 'FAILED:x'])->assertStatus(401);

    expect($avisos)->toHaveCount(0);
});

it('recusa tipo que o Itaú não avisa nesta rota', function () {
    $avisos = avisosItau();

    $this->postJson('/api/webhooks/banks/itau/42/cobranca', ['x' => 1], ['X-SSL-Client-Verify' => 'SUCCESS'])
        ->assertStatus(404);

    expect($avisos)->toHaveCount(0);
});

it('monta a URL que vai para o cadastro do webhook da chave', function () {
    expect(WebhookUrl::for('itau', 42, 'pix'))->toBe('https://erp.exemplo.com.br/api/webhooks/banks/itau/42/pix');
});
