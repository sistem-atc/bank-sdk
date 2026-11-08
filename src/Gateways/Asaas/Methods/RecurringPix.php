<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\RecurringPix\ListRecurrencesRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\RecurringPix\ListRecurrenceResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\RecurringPix\SingleRecurrenceResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\RecurringPix\ListRecurrencesItemsRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\RecurringPix\ListItemsRecurrenceResponseDTO;

class RecurringPix extends BaseMethods
{
    public function listRecurrences(ListRecurrencesRequestDTO $data): ListRecurrenceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/pix/transactions/recurrings', $data);
        return ListRecurrenceResponseDTO::fromArray($response);
    }
    
    public function retrieveSingleRecurrence(string $id): SingleRecurrenceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/pix/transactions/recurrings/{$id}");
        return SingleRecurrenceResponseDTO::fromArray($response);
    }
    
    public function cancelRecurrence(string $id): SingleRecurrenceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/pix/transactions/recurrings/{$id}/cancel");
        return SingleRecurrenceResponseDTO::fromArray($response);
    }
    
    public function listRecurrenceItems(string $id, ListRecurrencesItemsRequestDTO $data): ListItemsRecurrenceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/pix/transactions/recurrings/{$id}/items", $data);
        return ListItemsRecurrenceResponseDTO::fromArray($response);
    }
    
    public function cancelRecurrenceItem(string $id): SingleRecurrenceResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/pix/transactions/recurrings/items/{$id}/cancel");
        return SingleRecurrenceResponseDTO::fromArray($response);
    }
}