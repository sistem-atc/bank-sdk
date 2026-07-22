<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Codigos CBLC (codigo do investidor) vinculados ao CPF/CNPJ.
 *
 * Origem: POST /managers-cust-access-info/v1/searchcblc/{cpfCnpj}
 */
final class CblcsResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Lista de codigos CBLC (inteiros). @var array<int, mixed> */
        public readonly array $cblcs = [],
    ) {}
}
