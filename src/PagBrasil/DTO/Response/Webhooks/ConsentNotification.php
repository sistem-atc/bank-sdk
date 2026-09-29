<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;

/**
 * Webhook de consentimento Pix Automático (action=consent). `status`:
 * created, authorized, rejected, canceled. É a referência primária do ciclo
 * de vida do consentimento.
 */
final class ConsentNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $action = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $pixRecId = null,
        public readonly ?string $status = null,
        public readonly ?string $signature = null,
    ) {}

    public function isAuthorized(): bool
    {
        return strcasecmp((string) $this->status, 'authorized') === 0;
    }
}
