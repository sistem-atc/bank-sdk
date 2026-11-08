<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer\ListTransferRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Transfer\ListTransferResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Transfer\TransferAsaasResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Transfer\TranferAnotherResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer\TransferAsaasAccountRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer\TransferAnotherInstitutionRequestDTO;

class Transfer extends BaseMethods
{
    public function transferAnotherInstitutionAccountOrPixKey(TransferAnotherInstitutionRequestDTO $data): TranferAnotherResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/transfers", $data);
        return TranferAnotherResponseDTO::fromArray($response);
    }

    public function listTransfers(ListTransferRequestDTO $data): ListTransferResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/transfers', $data);
        return ListTransferResponseDTO::fromArray($response);
    }
    
    public function transferAsaasAccount(TransferAsaasAccountRequestDTO $data): TransferAsaasResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/transfers", $data);
        return TransferAsaasResponseDTO::fromArray($response);
    }
    
    public function retrieveSingleTransfer(string $id): TranferAnotherResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/transfers/{$id}");
        return TranferAnotherResponseDTO::fromArray($response);
    }
    
    public function cancelTransfer(string $id): TranferAnotherResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/transfers/{$id}/cancel");
        return TranferAnotherResponseDTO::fromArray($response);
    }
}