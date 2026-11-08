<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class AutomaticAnticipationConfigResponseDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?bool $creditCardAutomaticEnabled = null,
    ) {}
}