<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Devedor (usuário pagador) do Pix Automático. Objeto polimórfico: em
 * `vinculo.devedor` da recorrência vêm `cpf`/`nome`; na cobrança recorrente
 * (`cobr.devedor`) vêm os dados de endereço (`cep`, `cidade`, `logradouro`,
 * `uf`, `email`). Reúne todos os campos possíveis.
 */
final class Devedor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $cep = null,
        public readonly ?string $cidade = null,
        public readonly ?string $logradouro = null,
        public readonly ?string $uf = null,
    ) {}
}
