<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

/**
 * O host devolve a integração dona de um aviso, pelo id que está na URL do
 * webhook. Só é preciso pra banco que confere a origem com um segredo DA
 * INTEGRAÇÃO (o token do webhook do Asaas); quem confere por certificado (o
 * Inter) não usa.
 *
 * O host registra a implementação no container:
 *
 *   $this->app->bind(WebhookIntegrationResolver::class, MeuResolver::class);
 *
 * Sem ela, a rota desses bancos responde 503 (recebimento não configurado).
 */
interface WebhookIntegrationResolver
{
    /** null quando a integração não existe ou não é daquele banco. */
    public function resolve(string $bank, string $integrationId): ?BankIntegration;
}
