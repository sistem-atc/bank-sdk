<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/** @property list<BillingCycleOption> $data */
final class BillingCycleOptionList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<BillingCycleOption> $data */
    public function __construct(
        #[ArrayOf(BillingCycleOption::class)]
        public readonly array $data = [],
    ) {}
}
