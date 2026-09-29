<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Página de GET /subscriptions (sempre de UM cliente, por tax_id e/ou email).
 * Mais recente primeiro.
 *
 * @property list<Subscription> $data
 */
final class SubscriptionList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<Subscription> $data */
    public function __construct(
        #[ArrayOf(Subscription::class)]
        public readonly array $data = [],
        public readonly int $page = 1,
        public readonly int $perPage = 24,
        public readonly int $total = 0,
        public readonly int $totalPages = 0,
    ) {}
}
