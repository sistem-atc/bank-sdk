<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook\Split;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Fine;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Discount;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Interest;

class Subscription implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $id = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $customer = null,
        public readonly ?string $paymentLink = null,
        public readonly ?float $value = null,
        public readonly ?string $nextDueDate = null,
        public readonly ?string $cycle = null,
        public readonly ?string $description = null,
        public readonly ?string $billingType = null,
        public readonly ?bool $deleted = null,
        public readonly ?string $status = null,
        public readonly ?string $externalReference = null,
        public readonly ?bool $sendPaymentByPostalService = null,
        public readonly ?Discount $discount = null,
        public readonly ?Fine $fine = null,
        public readonly ?Interest $interest = null,
        #[ArrayOf(Split::class)] public readonly ?array $split = null,
    ) {}
}