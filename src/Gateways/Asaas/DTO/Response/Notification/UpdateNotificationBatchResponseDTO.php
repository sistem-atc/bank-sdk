<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Notification;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Notification;

final class UpdateNotificationBatchResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        #[ArrayOf(Notification::class)] public readonly ?array $notifications = null,
    ) {}
}