<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpSubServicoResponse.
 */
final class SpSubServicoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoSubServico = null,  // ex.: "Exame Habilitacao Teorico"
        public readonly ?float $valorTarifaPostagem = null,  // ex.: 10.01
        public readonly ?float $valorSubServico = null,  // ex.: 50.9
        public readonly ?int $codigoReceita = null,  // ex.: 4250
        public readonly ?float $valorTotal = null,  // ex.: 60.01
        public readonly ?string $codigoTipoEntrega = null,  // ex.: "2"
        public readonly ?int $codigoSubServico = null,  // ex.: 5
        public readonly ?int $codigoTipoIdentificacao = null,  // ex.: 1
    ) {}
}
