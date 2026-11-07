<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `LoteCobVConsultado` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `LoteCobVConsultado`).
 *
 * @property array<int, LoteCobVItem> $cobsv
 */
final class LoteCobVConsultado implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $descricao = null,
        /** Timestamp que indica o momento em que foi criado o lote. Respeita o formato definido na RFC 3339. */
        public readonly ?string $criacao = null,
        #[ArrayOf(LoteCobVItem::class)]
        public readonly array $cobsv = [],
    ) {}
}
