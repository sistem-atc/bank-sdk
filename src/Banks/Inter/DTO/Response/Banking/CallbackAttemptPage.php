<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CallbackAttemptPage` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CallbackAttemptPage`).
 *
 * @property array<int, CallbackAttemptPageItem> $data
 */
final class CallbackAttemptPage implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalElementos = null,
        public readonly ?int $totalPaginas = null,
        /** Informa se é a primeira página */
        public readonly ?bool $primeiraPagina = null,
        /** Informa se é a última página */
        public readonly ?bool $ultimaPagina = null,
        /** Dados de cada notificação de callback. */
        #[ArrayOf(CallbackAttemptPageItem::class)]
        public readonly array $data = [],
    ) {}
}
