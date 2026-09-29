<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança dentro do snapshot de recorrência. `status`: pending, success ou
 * error; `type`: credit_card, payment_link, pix_automatico (null enquanto
 * indefinido).
 *
 * @property list<ChargedCard> $chargedCards
 */
final class RecurrenceCharge implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<ChargedCard> $chargedCards */
    public function __construct(
        public readonly ?int $chargeNumber = null,
        public readonly ?bool $attempted = null,
        public readonly ?string $status = null,
        public readonly ?string $type = null,
        public readonly ?string $attemptedAt = null,
        public readonly ?string $scheduledFor = null,
        #[ArrayOf(ChargedCard::class)]
        public readonly array $chargedCards = [],
    ) {}
}
