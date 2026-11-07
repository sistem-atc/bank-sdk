<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Multa aplicada à cobrança
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobVValorMulta`).
 */
final class CobVValorMulta implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** ##### Modalidade da multa, conforme tabela de domínios. Descrição Domínio Valor Fixo 1 Percentual 2 */
        public readonly ?int $modalidade = null,
        /** Multa do documento em valor absoluto ou percentual, conforme "valor.multa.modalidade". */
        public readonly ?string $valorPerc = null,
    ) {}
}
