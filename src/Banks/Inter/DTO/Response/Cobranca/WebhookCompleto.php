<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `WebhookCompleto` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `WebhookCompleto`).
 */
final class WebhookCompleto implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $webhookUrl = null,
        /** Data e hora em que o webhook foi cadastrado. Respeita RFC 3339. */
        public readonly ?string $criacao = null,
        /** Data e hora em que o webhook foi atualizado. Respeita RFC 3339. */
        public readonly ?string $atualizacao = null,
    ) {}
}
