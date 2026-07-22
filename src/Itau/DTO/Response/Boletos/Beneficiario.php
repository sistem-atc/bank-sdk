<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `beneficiario` das respostas de Boletos Cobrança. `id_beneficiario` é
 * Agência(4) + Conta(7) + DAC(1).
 */
final class Beneficiario implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idBeneficiario = null,
        public readonly ?string $nomeCobranca = null,
        public readonly ?TipoPessoa $tipoPessoa = null,
        public readonly ?Endereco $endereco = null,
    ) {}
}
