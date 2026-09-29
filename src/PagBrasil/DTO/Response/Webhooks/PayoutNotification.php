<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;

/**
 * Webhook de payout: action addpayout (solicitado), successpayout
 * (concluído) ou failpayout (falhou). `date` em "Y-m-d H:i:s".
 */
final class PayoutNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $action = null,
        public readonly ?string $taxid = null,
        public readonly ?string $id = null,
        public readonly ?string $amount = null,
        public readonly ?string $date = null,
        public readonly ?string $signature = null,
    ) {}

    public function isCompleted(): bool
    {
        return $this->action === 'successpayout';
    }

    public function isFailed(): bool
    {
        return $this->action === 'failpayout';
    }
}
