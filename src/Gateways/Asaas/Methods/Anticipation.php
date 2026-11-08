<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation\ListAnticipationResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\RequestAnticipationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\SimulateAnticipationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation\SimulateAnticipationResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation\RetrieveAnticipationResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\ListAnticipationFilterRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\UpdateAutomaticAnticipationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation\RetrieveAnticipationLimitsResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation\AutomaticAnticipationConfigResponseDTO;

class Anticipation extends BaseMethods
{

    public function retrieveSingleAnticipation(string $anticipationId): RetrieveAnticipationResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/anticipations/{$anticipationId}");
        return RetrieveAnticipationResponseDTO::fromArray($response);
    }

    public function requestAnticipation(RequestAnticipationRequestDTO $data): RetrieveAnticipationResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/anticipations", $data);
        return RetrieveAnticipationResponseDTO::fromArray($response);
    }

    public function listAnticipations(ListAnticipationFilterRequestDTO $data): ListAnticipationResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/anticipations', $data);
        return ListAnticipationResponseDTO::fromArray($response);
    }

    public function simulateAnticipation(SimulateAnticipationRequestDTO $data): SimulateAnticipationResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/anticipations/simulate", $data);
        return SimulateAnticipationResponseDTO::fromArray($response);
    }

    public function updateStatusAutomaticAnticipation(UpdateAutomaticAnticipationRequestDTO $data): AutomaticAnticipationConfigResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::PUT, "/anticipations/configurations", $data);
        return AutomaticAnticipationConfigResponseDTO::fromArray($response);
    }

    public function retrieveStatusAutomaticAnticipation(): AutomaticAnticipationConfigResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/anticipations/configurations");
        return AutomaticAnticipationConfigResponseDTO::fromArray($response);
    }

    public function retrieveAnticipationLimits(): RetrieveAnticipationLimitsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/anticipations/limits");
        return RetrieveAnticipationLimitsResponseDTO::fromArray($response);
    }

    public function cancelAnticipation(string $anticipationId): RetrieveAnticipationResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/anticipations/{$anticipationId}/cancel");
        return RetrieveAnticipationResponseDTO::fromArray($response);
    }
}