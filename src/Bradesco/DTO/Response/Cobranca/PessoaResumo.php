<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Pessoa resumida (CPF/CNPJ decomposto + nome) usada nas LISTAS de títulos
 * pendentes de liquidação.
 * Origem: POST /boleto/cobranca-pendente/v1/listar
 */
final class PessoaResumo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Raiz do CPF/CNPJ. */
        public readonly ?string $cnpjCpf = null,
        /** Filial do CNPJ. */
        public readonly ?string $filial = null,
        /** Dígito de controle. */
        public readonly ?string $controle = null,
        /** Nome/razão social. */
        public readonly ?string $nome = null,
    ) {}
}
