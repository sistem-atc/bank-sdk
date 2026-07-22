<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * CPF/CNPJ decomposto no formato do Bradesco (raiz + filial + dígito de
 * controle). Aparece em praticamente todo payload de Cobrança.
 *
 * Os campos vêm ora como string ora como inteiro conforme o microserviço —
 * aqui são sempre `?string` e o AutoHydrate faz o cast.
 */
final class CpfCnpj implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Raiz do CPF/CNPJ. */
        public readonly ?string $cpfCnpj = null,
        /** Filial do CNPJ (0 quando CPF). */
        public readonly ?string $filial = null,
        /** Dígito de controle do CPF/CNPJ. */
        public readonly ?string $controle = null,
    ) {}
}
