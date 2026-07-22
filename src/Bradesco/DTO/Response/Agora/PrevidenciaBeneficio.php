<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Beneficio implantado numa proposta de previdencia.
 *
 * Origem: components.schemas.ImplantedBenefit.
 */
final class PrevidenciaBeneficio implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo do beneficio. */
        public readonly ?int $benefitCode = null,
        /** Descricao do beneficio. */
        public readonly ?string $descriptionBenefit = null,
        /** Tipo do beneficio. */
        public readonly ?string $typeBenefit = null,
        /** Saldo atual do beneficio (string no contrato). */
        public readonly ?string $valueCurrentBalanceBenefit = null,
    ) {}
}
