<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentLink;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Image;

final class ImagePaymentLinkResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?bool $main = null,
        public readonly ?Image $image = null,
    ) {}
}