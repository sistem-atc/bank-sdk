<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\MobilePhoneRecharge;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\CellPhoneCredits;

final class SearchCellPhoneResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $name = null,
        #[ArrayOf(CellPhoneCredits::class)] public readonly ?array $values = null,
    ) {}
}