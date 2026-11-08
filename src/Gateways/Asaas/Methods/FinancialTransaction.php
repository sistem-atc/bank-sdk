<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FinancialTransaction\RetrieveExtractRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\FinancialTransaction\RetrieveExtractResponseDTO;

class FinancialTransaction extends BaseMethods
{
    public function retrieveExtract(RetrieveExtractRequestDTO $data): RetrieveExtractResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/financialTransactions', $data);
        return RetrieveExtractResponseDTO::fromArray($response);
    }
}