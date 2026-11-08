<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

/**
 * Credenciais do Asaas, além das do BankIntegration.
 *
 * O Asaas não usa OAuth: toda chamada leva a chave de API da conta no
 * cabeçalho `access_token` (gerada em Integrações, no painel). O webhook é
 * cadastrado no painel do Asaas com um token de autenticação escolhido pelo
 * cliente, que volta em todo aviso no cabeçalho `asaas-access-token` — é com
 * ele que o SDK confere a origem.
 *
 * Ambiente pela integração (isSandbox()). client_id/client_secret não são
 * usados.
 */
interface AsaasIntegration extends BankIntegration
{
    /** Chave de API da conta Asaas (cabeçalho `access_token`). */
    public function getApiKey(): string;

    /**
     * Token de autenticação do webhook cadastrado no painel do Asaas. null =
     * não cadastrado, e o SDK recusa todo aviso dessa integração.
     */
    public function getWebhookToken(): ?string;
}
