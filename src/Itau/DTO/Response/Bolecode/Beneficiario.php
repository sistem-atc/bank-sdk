<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `beneficiario` do Bolecode Pix — emissor do boleto. Na emissão vai só
 * `id_beneficiario` (Agência 4 + Conta 7 + DAC 1); no body de saída a API
 * devolve `nome_cobranca` e o `tipo_pessoa` do beneficiário.
 */
final class Beneficiario implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idBeneficiario = null,
        public readonly ?string $nomeCobranca = null,
        public readonly ?TipoPessoa $tipoPessoa = null,
    ) {}
}
