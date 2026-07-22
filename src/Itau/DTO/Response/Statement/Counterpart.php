<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `counterpart` de um lançamento do Extrato Itaú (Account Statement) — a
 * contraparte da movimentação (quem pagou/recebeu). `document` costuma vir
 * mascarado ("***.456.789-**") e `person` ∈ {FISICA, JURIDICA}.
 */
final class Counterpart implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $ispb = null,
        public readonly ?string $agency = null,
        public readonly ?string $account = null,
        public readonly ?string $digit = null,
        public readonly ?string $name = null,
        public readonly ?string $institution = null,
        public readonly ?string $document = null,
        public readonly ?string $person = null,
    ) {}
}
