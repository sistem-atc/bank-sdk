<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Carteira administrada vinculada ao perfil do investidor.
 *
 * Origem: components.schemas.PorfoliosApiResponse.
 */
final class PerfilCarteira implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo da carteira administrada. */
        public readonly ?int $portfolioManagementCodeApi = null,
        /** Descricao da carteira administrada. */
        public readonly ?string $portfolioManagementDescriptionApi = null,
        /** Codigo de enquadramento (conformidade). */
        public readonly ?int $conformityIdentifierCode = null,
    ) {}
}
