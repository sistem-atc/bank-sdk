<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Histórico de tentativas de uma recorrência (mais antiga primeiro, sem
 * paginação) ou página de GET /charges (mais recente primeiro, paginada).
 *
 * @property list<ChargeAttempt> $data
 */
final class ChargeAttemptList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<ChargeAttempt> $data */
    public function __construct(
        #[ArrayOf(ChargeAttempt::class)]
        public readonly array $data = [],
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?int $total = null,
        public readonly ?int $totalPages = null,
    ) {}
}
