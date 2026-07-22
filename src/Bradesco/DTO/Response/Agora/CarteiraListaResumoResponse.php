<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Detalhamento da carteira por classe de ativo.
 *
 * Origem: POST /managers-portfolio-mgmt/v1/listsummary/{cpfCnpj}/{accountCode}
 *      e  POST /managers-portfolio-mgmt/v1/listsummaryLessPrev/{cpfCnpj}/{accountCode}
 */
final class CarteiraListaResumoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Resultado consolidado. */
        public readonly ?CarteiraListaResumoResultado $result = null,
    ) {}
}
