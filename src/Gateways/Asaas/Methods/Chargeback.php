<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Chargeback\ChargebackResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Chargeback\ListChargebacksRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Chargeback\ListChargebackResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Chargeback\ChargebackDisputeResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Chargeback\CreateChargebackDisputeRequestDTO;

class Chargeback extends BaseMethods
{

    public function createChargebackDispute(string $id, CreateChargebackDisputeRequestDTO $data): ChargebackDisputeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/chargebacks/{$id}/dispute", $data);
        return ChargebackDisputeResponseDTO::fromArray($response);
    }

    public function listChargebacks(ListChargebacksRequestDTO $data): ListChargebackResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/chargebacks', $data);
        return ListChargebackResponseDTO::fromArray($response);
    }

    public function retrieveSingleChargeback(?string $id): ChargebackResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/{$id}/chargeback");
        return ChargebackResponseDTO::fromArray($response);
    }
}