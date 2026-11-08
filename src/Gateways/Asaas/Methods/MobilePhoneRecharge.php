<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\MobilePhoneRecharge\RechargeRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\MobilePhoneRecharge\RechargeResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\MobilePhoneRecharge\ListCellPhonesRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\MobilePhoneRecharge\ListCellPhonesResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\MobilePhoneRecharge\SearchCellPhoneResponseDTO;

class MobilePhoneRecharge extends BaseMethods
{
    public function requestRecharge(RechargeRequestDTO $data): RechargeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/mobilePhoneRecharges", $data);
        return RechargeResponseDTO::fromArray($response);
    }
    
    public function listCellPhoneTopups(ListCellPhonesRequestDTO $data): ListCellPhonesResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/mobilePhoneRecharges", $data);
        return ListCellPhonesResponseDTO::fromArray($response);
    }
    
    public function recoverSingleCellPhoneRecharge(string $id): RechargeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/mobilePhoneRecharges/{$id}");
        return RechargeResponseDTO::fromArray($response);
    }
    
    public function cancelCellPhoneRecharge(string $id): RechargeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/mobilePhoneRecharges/{$id}/cancel");
        return RechargeResponseDTO::fromArray($response);
    }
    
    public function searchCellPhoneProvider(string $phoneNumber): SearchCellPhoneResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/mobilePhoneRecharges/{$phoneNumber}/provider");
        return SearchCellPhoneResponseDTO::fromArray($response);
    }
}