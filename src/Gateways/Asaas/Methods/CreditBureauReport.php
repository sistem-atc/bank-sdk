<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\CreditBureauReport\MakeConsultationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\CreditBureauReport\MakeConsultationResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\CreditBureauReport\ListCreditBureauReportsRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\CreditBureauReport\ListCreditBureauReportsResponseDTO;

class CreditBureauReport extends BaseMethods
{

    public function makeConsultation(MakeConsultationRequestDTO $data): MakeConsultationResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/creditBureauReport", $data);
        return MakeConsultationResponseDTO::fromArray($response);
    }

    public function listCreditBureauReports(ListCreditBureauReportsRequestDTO $data): ListCreditBureauReportsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/creditBureauReport', $data);
        return ListCreditBureauReportsResponseDTO::fromArray($response);
    }

    public function retrieveCreditBureauReport(string $id): MakeConsultationResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/creditBureauReport/{$id}");
        return MakeConsultationResponseDTO::fromArray($response);
    }
}