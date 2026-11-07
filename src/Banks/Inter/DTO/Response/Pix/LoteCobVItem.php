<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `LoteCobVItem` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `LoteCobVItem`).
 */
final class LoteCobVItem implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $txid = null,
        public readonly ?string $status = null,
        public readonly ?Problema $problema = null,
        /** Data e hora em que a cobrança foi criada. Respeita RFC 3339. */
        public readonly ?string $criacao = null,
    ) {}
}
