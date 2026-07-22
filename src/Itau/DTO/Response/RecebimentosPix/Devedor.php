<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `devedor` de uma cobrança Pix (quem vai pagar). Pessoa física traz
 * `cpf`; pessoa jurídica traz `cnpj` — nunca os dois simultaneamente.
 */
final class Devedor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $nome = null,
        public readonly ?string $logradouro = null,
        public readonly ?string $cidade = null,
        public readonly ?string $uf = null,
        public readonly ?string $cep = null,
        public readonly ?string $email = null,
    ) {}
}
