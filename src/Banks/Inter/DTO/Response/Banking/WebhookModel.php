<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `WebhookModel` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `WebhookModel`).
 */
final class WebhookModel implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** URL de configuração do webhook. Deve iniciar obrigatoriamente com **https://** */
        public readonly ?string $webhookUrl = null,
        /** Data e hora em que o webhook foi cadastrado. Representada de acordo com a [RFC3339](https://tools.ietf.org/html/rfc3339) */
        public readonly ?string $criacao = null,
    ) {}
}
