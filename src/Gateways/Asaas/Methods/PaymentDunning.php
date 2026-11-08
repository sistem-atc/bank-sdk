<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\ListsDunningRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDunning\HistoryListResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\ResendDocumentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\PaymentDunningRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDunning\PaymentDunningResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\ListPaymentDunningRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDunning\ListPaymentDunningResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDunning\ListPaymentReceivedResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\SimulatePaymentDunningRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDunning\ListPaymentsAvaliableResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDunning\SimulatePaymentDunningResponseDTO;

class PaymentDunning extends BaseMethods
{
    public function createPaymentDunning(PaymentDunningRequestDTO $data): PaymentDunningResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/paymentDunnings", $data);
        return PaymentDunningResponseDTO::fromArray($response);
    }
    
    public function listPaymentDunnings(ListPaymentDunningRequestDTO $data): ListPaymentDunningResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/paymentDunnings', $data);
        return ListPaymentDunningResponseDTO::fromArray($response);
    }
    
    public function simulatePaymentDunning(SimulatePaymentDunningRequestDTO $data): SimulatePaymentDunningResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/paymentDunnings', $data);
        return SimulatePaymentDunningResponseDTO::fromArray($response);
    }
    
    public function recoverSinglePaymentDunning(string $id): PaymentDunningResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/paymentDunnings/{$id}');
        return PaymentDunningResponseDTO::fromArray($response);
    }
    
    public function eventHistoryLists(string $id, ListsDunningRequestDTO $data): HistoryListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/paymentDunnings/{$id}/history", $data);
        return HistoryListResponseDTO::fromArray($response);
    }
    
    public function listPaymentsReceived(string $id, ListsDunningRequestDTO $data): ListPaymentReceivedResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/paymentDunnings/{$id}/partialPayments", $data);
        return ListPaymentReceivedResponseDTO::fromArray($response);
    }
    
    public function listPaymentsAvailablePaymentDunning(ListsDunningRequestDTO $data): ListPaymentsAvaliableResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/paymentDunnings/paymentsAvailableForDunning", $data);
        return ListPaymentsAvaliableResponseDTO::fromArray($response);
    }
    
    public function resendDocuments(string $id, ResendDocumentRequestDTO $data): PaymentDunningResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/paymentDunnings/{$id}/documents", $data);
        return PaymentDunningResponseDTO::fromArray($response);
    }
    
    public function cancelPaymentDunning(string $id): PaymentDunningResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/paymentDunnings/{$id}/cancel");
        return PaymentDunningResponseDTO::fromArray($response);
    }   
}