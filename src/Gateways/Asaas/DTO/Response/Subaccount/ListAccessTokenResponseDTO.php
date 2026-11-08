<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Subaccount;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListAccessTokenResponseDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        #[ArrayOf(UpdateApiKeySubAccountResponseDTO::class)] public readonly ?array $accessTokens = null,
    ) {}
}