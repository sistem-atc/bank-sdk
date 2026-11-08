<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Checkout\CheckoutResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Checkout\CreateNewCheckoutRequestDTO;

class Checkout extends BaseMethods
{

    public function createCheckout(CreateNewCheckoutRequestDTO $data): CheckoutResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/checkouts", $data);
        return CheckoutResponseDTO::fromArray($response);
    }

    public function cancelCheckout(string $id): CheckoutResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/checkouts/{$id}/cancel");
        return CheckoutResponseDTO::fromArray($response);
    }
}