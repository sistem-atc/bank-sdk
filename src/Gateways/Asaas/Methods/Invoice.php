<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Invoice\InvoiceResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\ListInvoicesRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\CancelInvoiceRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\UpdateInvoiceRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Invoice\ListInvoiceResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\ScheduleInvoiceRequestDTO;

class Invoice extends BaseMethods
{
    public function scheduleInvoice(ScheduleInvoiceRequestDTO $data): InvoiceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/invoices", $data);
        return InvoiceResponseDTO::fromArray($response);
    }

    public function listInvoices(ListInvoicesRequestDTO $data): ListInvoiceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/invoices", $data);
        return ListInvoiceResponseDTO::fromArray($response);
    }
    
    public function updateInvoice(string $id, UpdateInvoiceRequestDTO $data): InvoiceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::PUT, "/invoices/$id", $data);
        return InvoiceResponseDTO::fromArray($response);
    }
    
    public function retrieveSingleInvoice(string $id): InvoiceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/invoices/{$id}");
        return InvoiceResponseDTO::fromArray($response);
    }
    
    public function issueInvoice(string $id): InvoiceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/invoices/{$id}/authorize");
        return InvoiceResponseDTO::fromArray($response);
    }
    
    public function cancelInvoice(string $id, CancelInvoiceRequestDTO $data): InvoiceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/invoices/{$id}/cancel", $data);
        return InvoiceResponseDTO::fromArray($response);
    }
}