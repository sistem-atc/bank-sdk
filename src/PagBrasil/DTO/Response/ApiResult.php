<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta de operação cujo formato a doc da PagBrasil não fixa (ex.:
 * retentativa de cobrança Pix Automático): o texto cru e, se veio estrutura
 * (XML/JSON), os campos.
 */
final class ApiResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<string, mixed> $data */
    public function __construct(
        public readonly string $format = 'text',
        public readonly string $message = '',
        public readonly array $data = [],
    ) {}
}
