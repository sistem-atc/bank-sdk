<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentLink;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class AddImagePaymentLinkRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;

    public function __construct(
        public readonly ?bool $main = null,
        #[MultipartFile(as: 'image')] public readonly ?string $image = null,
    ) {}
}