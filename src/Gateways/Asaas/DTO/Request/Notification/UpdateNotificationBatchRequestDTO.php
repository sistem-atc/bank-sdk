<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Notification;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class UpdateNotificationBatchRequestDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $customer,
        #[ArrayOf(NotificationRequestDTO::class)] public readonly ?array $notifications = null,
    ) {}
}