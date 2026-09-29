<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PagStream;

use SistemAtc\Banks\PagBrasil\Bases\FormMethods;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\LegacySubscription;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\LegacySubscriptionCreated;

/**
 * As rotas do PagStream que ainda não migraram pra v2 — form-urlencoded com
 * secret/pbtoken no corpo, resposta XML (ou JSON). Usadas por
 * SubscriptionsMethods; não há motivo pra chamar direto.
 */
final class LegacyMethods extends FormMethods
{
    /** @param array<string, mixed> $dados */
    public function criarAssinatura(array $dados): LegacySubscriptionCreated
    {
        $parsed = $this->post('/api/pagstream/subscription/add', $dados);

        return LegacySubscriptionCreated::fromArray($this->expectStructure($parsed));
    }

    /**
     * @param  'add'|'update'|'delete'  $acao
     * @param  array<string, mixed>  $dados
     */
    public function item(string $acao, string $subscription, string $sku, array $dados = []): LegacySubscription
    {
        $parsed = $this->post("/api/pagstream/subscription/item/{$acao}", [
            'subscription' => $subscription,
            'sku' => $sku,
        ] + ($acao === 'delete' ? [] : $dados));

        return LegacySubscription::fromArray($this->expectStructure($parsed));
    }
}
