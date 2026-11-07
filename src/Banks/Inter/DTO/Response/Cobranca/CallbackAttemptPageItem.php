<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CallbackAttemptPageItem` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CallbackAttemptPageItem`).
 *
 * @property array<int, CallbackCobranca> $payload
 */
final class CallbackAttemptPageItem implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** URL do webhook no momento do disparo. */
        public readonly ?string $webhookUrl = null,
        #[ArrayOf(CallbackCobranca::class)]
        public readonly array $payload = [],
        /** Qual o índice da tentativa de disparo. */
        public readonly ?int $numeroTentativa = null,
        /** Data/hora da tentativa de disparo do callback. */
        public readonly ?string $dataHoraDisparo = null,
        /** Informa se obteve sucesso no disparo do callback. */
        public readonly ?bool $sucesso = null,
        /** Resposta da chamada ao endpoint do webhook. */
        public readonly ?int $httpStatus = null,
        /** Mensagem de erro em caso de falha no dispardo do callback. */
        public readonly ?string $mensagemErro = null,
    ) {}
}
