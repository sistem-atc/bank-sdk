<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `recebedor` de uma cobrança Pix (o titular da conta Itaú que recebe).
 */
final class Recebedor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cnpj = null,
        public readonly ?string $cpf = null,
        public readonly ?string $nome = null,
        public readonly ?string $nomeFantasia = null,
        public readonly ?string $logradouro = null,
        public readonly ?string $cidade = null,
        public readonly ?string $uf = null,
        public readonly ?string $cep = null,
    ) {}
}
