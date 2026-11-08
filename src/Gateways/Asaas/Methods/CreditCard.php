<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\CreditCard as CommonCreditCard;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\CreditCard\CreditCardTokenizationRequestDTO;

class CreditCard extends BaseMethods
{
    public function tokenization(CreditCardTokenizationRequestDTO $data): CommonCreditCard
    {
        $response = $this->makeRequest(HttpMethod::POST, '/creditCard/tokenization', $data);
        return CommonCreditCard::fromArray($response);
    }
}