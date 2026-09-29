<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\PagBrasil\Enums\RecurrenceCycle;

/**
 * Recorrência (consentimento) Pix Automático — /api/pix/rec/get.
 * `status`: Authorized, Canceled ou NotInitiated.
 */
final class Recurrence implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $pixRecId = null,
        public readonly ?string $status = null,
        public readonly ?string $pixRecDescription = null,
        public readonly ?string $pixRecCycle = null,
        public readonly ?string $pixRecExpiration = null,
        public readonly ?string $pixRecMinimumAmount = null,
        public readonly ?string $signature = null,
    ) {}

    public function isAuthorized(): bool
    {
        return strcasecmp((string) $this->status, 'Authorized') === 0;
    }

    public function cycle(): ?RecurrenceCycle
    {
        return $this->pixRecCycle === null ? null : RecurrenceCycle::tryFrom($this->pixRecCycle);
    }
}
