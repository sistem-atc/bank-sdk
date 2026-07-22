<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Grupo de ativos na alocacao da carteira.
 *
 * Origem: components.schemas.PortfolioSummaryAssetGroupApi.
 */
final class CarteiraGrupoAtivo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo do grupo. */
        public readonly ?string $code = null,
        /** Descricao do grupo. */
        public readonly ?string $description = null,
        /** Patrimonio bruto do grupo. */
        public readonly ?float $grossPatrimony = null,
        /** Percentual do grupo no patrimonio. */
        public readonly ?float $percentage = null,
    ) {}
}
