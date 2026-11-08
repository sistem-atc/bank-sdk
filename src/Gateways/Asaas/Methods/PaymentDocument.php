<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDocument\PaymentDocumentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDocument\ListPaymentDocumentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDocument\UploadPaymentDocumentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDocument\UpdateSettingsDocumentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDocument\DeletePaymentDocumentResponseDTO;

class PaymentDocument extends BaseMethods
{
    public function uploadPaymentDocuments(string $id, UploadPaymentDocumentRequestDTO $data): PaymentDocumentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/payments/{$id}/documents", $data);
        return PaymentDocumentResponseDTO::fromArray($response);
    }
    
    public function listDocumentsPayment(string $id): ListPaymentDocumentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/{$id}/documents");
        return ListPaymentDocumentResponseDTO::fromArray($response);
    }
    
    public function updateSettingsaDocumentPayment(string $id, string $documentId, UpdateSettingsDocumentRequestDTO $data): PaymentDocumentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::PUT, "/payments/{$id}/documents/{$documentId}", $data);
        return PaymentDocumentResponseDTO::fromArray($response);
    }
    
    public function retrieveSingleDocumentPayment(string $id, string $documentId): PaymentDocumentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/{$id}/documents/{$documentId}");
        return PaymentDocumentResponseDTO::fromArray($response);
    }
    
    public function deleteDocumentFromPayment(string $id, string $documentId): DeletePaymentDocumentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/payments/{$id}/documents/{$documentId}");
        return DeletePaymentDocumentResponseDTO::fromArray($response);
    }
}