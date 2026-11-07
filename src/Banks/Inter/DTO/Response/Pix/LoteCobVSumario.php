<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `LoteCobVSumario` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `LoteCobVSumario`).
 */
final class LoteCobVSumario implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dataCriacaoProcessamento = null,
        public readonly ?string $statusProcessamento = null,
        public readonly ?int $totalCobrancas = null,
        public readonly ?int $totalCobrancasNegadas = null,
        public readonly ?int $totalCobrancasCriadas = null,
    ) {}
}
