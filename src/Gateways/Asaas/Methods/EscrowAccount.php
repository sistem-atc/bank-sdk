<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\EscrowAccount\EscrowResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\EscrowAccount\EscrowAccountRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\EscrowAccount\EscrowAccountResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\EscrowAccount\FinishPaymentEscrowResponseDTO;

class EscrowAccount extends BaseMethods
{

    public function SaveOrUpdateEscrowAccount(string $id, EscrowAccountRequestDTO $data): EscrowAccountResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/accounts/{$id}/escrow", $data);
        return EscrowAccountResponseDTO::fromArray($response);
    }

    public function CreateDefaultEscrowAccount(EscrowAccountRequestDTO $data): EscrowAccountResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/accounts/escrow", $data);
        return EscrowAccountResponseDTO::fromArray($response);
    }

    public function FinishPaymentEscrow(string $id): FinishPaymentEscrowResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/escrow/{$id}/finish");
        return FinishPaymentEscrowResponseDTO::fromArray($response);
    }

    public function retrieveEscrowAccount(string $id): EscrowAccountResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/accounts/{$id}/escrow");
        return EscrowAccountResponseDTO::fromArray($response);
    }

    public function retrieveDefaultEscrowAccount(): EscrowAccountResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/accounts/escrow");
        return EscrowAccountResponseDTO::fromArray($response);
    }

    public function retrievePaymentEscrow(string $id): EscrowResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/{$id}/escrow");
        return EscrowResponseDTO::fromArray($response);
    }
}