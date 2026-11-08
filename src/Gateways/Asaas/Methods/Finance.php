<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Finance\BalanceResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Finance\CollectionStatisticsRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Finance\CollectionStatisticsResponseDTO;

class Finance extends BaseMethods
{
    public function RetrieveAccountBalance(): BalanceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/finance/balance");
        return BalanceResponseDTO::fromArray($response);
    }

    public function collectionsStatistics(CollectionStatisticsRequestDTO $data): CollectionStatisticsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/finance/payment/statistics', $data);
        return CollectionStatisticsResponseDTO::fromArray($response);
    }

    public function retrieveSplitValues(): CollectionStatisticsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/finance/split/statistics");
        return CollectionStatisticsResponseDTO::fromArray($response);
    }
}