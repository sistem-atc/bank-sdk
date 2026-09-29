<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Payout;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Favorecido do Payout (getpayee e webhook de favorecido). `status`:
 * 0 = pendente, 1 = aprovado, 2 = rejeitado. `accountType`: 1 = corrente,
 * 2 = poupança.
 */
final class Payee implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public const STATUS_PENDING = '0';

    public const STATUS_APPROVED = '1';

    public const STATUS_REJECTED = '2';

    public function __construct(
        public readonly ?string $taxid = null,
        public readonly ?string $bank = null,
        public readonly ?string $branch = null,
        public readonly ?string $account = null,
        public readonly ?string $accountType = null,
        public readonly ?string $status = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $street = null,
        public readonly ?string $zip = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $documentLink = null,
    ) {}

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
