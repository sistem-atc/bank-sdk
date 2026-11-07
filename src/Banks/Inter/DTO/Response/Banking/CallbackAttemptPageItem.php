<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CallbackAttemptPageItem` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CallbackAttemptPageItem`).
 */
final class CallbackAttemptPageItem implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** URL do webhook no momento do disparo. */
        public readonly ?string $webhookUrl = null,
        /** Corpo enviado na requisição. */
        public readonly array $payload = [],
        /** Qual o índice da tentativa de disparo. */
        public readonly ?int $numeroTentativa = null,
        /** Data/hora da tentativa de disparo do callback. */
        public readonly ?string $dataEnvio = null,
        public readonly ?bool $sucesso = null,
        /** Resposta da chamada ao endpoint do webhook. */
        public readonly ?int $httpStatus = null,
        public readonly ?string $mensagemErro = null,
    ) {}
}
