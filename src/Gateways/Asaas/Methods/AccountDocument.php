<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountDocument\SendDocumentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountDocument\SendDocumentsResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountDocument\RemoveDocumentsResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountDocument\CheckPendingDocumentsResponseDTO;

class AccountDocument extends BaseMethods
{

    public function checkPendingDocuments(): CheckPendingDocumentsResponseDTO
    {
        $response =  $this->makeRequest(HttpMethod::GET, "/myAccount/documents");
        return CheckPendingDocumentsResponseDTO::fromArray($response);
    }

    public function sendDocuments(string $id, SendDocumentRequestDTO $data): SendDocumentsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/myAccount/documents/{$id}", $data);
        return SendDocumentsResponseDTO::fromArray($response);
    }

    public function viewDocumentSent(string $documentId): SendDocumentsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/myAccount/documents/files/{$documentId}");
        return SendDocumentsResponseDTO::fromArray($response);
    }

    public function updateSentDocument(string $documentId, SendDocumentRequestDTO $data): SendDocumentsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/myAccount/documents/files/{$documentId}", $data);
        return SendDocumentsResponseDTO::fromArray($response);
    }

    public function removeSentDocument(string $documentId): RemoveDocumentsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/myAccount/documents/files/{$documentId}");
        return RemoveDocumentsResponseDTO::fromArray($response);
    }
}