<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\PaymentOverdue;
use SistemAtc\Banks\Gateways\Asaas\Enums\EventNotification;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Notification implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $id = null,
        public readonly ?string $customer = null,
        public readonly ?bool $enabled = null,
        public readonly ?bool $emailEnabledForProvider = null,
        public readonly ?bool $smsEnabledForProvider = null,
        public readonly ?bool $emailEnabledForCustomer = null,
        public readonly ?bool $smsEnabledForCustomer = null,
        public readonly ?bool $phoneCallEnabledForCustomer = null,
        public readonly ?bool $whatsappEnabledForCustomer = null,
        public readonly ?EventNotification $event = null,
        public readonly ?PaymentOverdue $scheduleOffset = null,
        public readonly ?bool $deleted = null,
    ) {}
}