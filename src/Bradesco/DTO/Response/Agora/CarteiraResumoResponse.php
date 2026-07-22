<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resumo consolidado da carteira por classe de ativo.
 *
 * Origem: POST /managers-portfolio-mgmt/v1/summary/{cpfCnpj}/{accountCode}
 */
final class CarteiraResumoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Alocacao por classe de ativo. */
        public readonly ?CarteiraAlocacao $allocation = null,
        /** Data de referencia da posicao. */
        public readonly ?string $referenceDate = null,
        /** Patrimonio bruto total. */
        public readonly ?float $totalGrossPatrimony = null,
    ) {}
}
