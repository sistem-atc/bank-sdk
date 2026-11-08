<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Split;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentSplit\ListSplitsRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentSplit\ListSplitResponseDTO;

class PaymentSplit extends BaseMethods
{
    public function retrieveSinglePaidSplit(string $id): Split
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/splits/paid/{$id}");
        return Split::fromArray($response);
    }
    
    public function listPaidSplits(ListSplitsRequestDTO $data): ListSplitResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/splits/paid", $data);
        return ListSplitResponseDTO::fromArray($response);
    }
    
    public function retrieveSingleReceivedSplit(string $id): Split
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/splits/received/{$id}");
        return Split::fromArray($response);
    }
    
    public function listReceivedSplits(ListSplitsRequestDTO $data)
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/splits/received", $data);
        return ListSplitResponseDTO::fromArray($response);
    }
}