<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Parâmetros ecoados pela consulta de transferências (janela + paginação).
 *
 * Schema `ParametrosResponse` da spec "Pix Transferência - Consultar".
 */
final class ParametrosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data inicial utilizada na consulta. RFC 3339. */
        public readonly ?string $inicio = null,
        /** Data final utilizada na consulta. RFC 3339. */
        public readonly ?string $fim = null,
        /** Status filtrado na consulta. */
        public readonly ?string $status = null,
        /** Bloco de paginação. */
        public readonly ?Paginacao $paginacao = null,
    ) {}
}
