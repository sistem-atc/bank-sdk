<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Customer\CustomerRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Customer\ListCustomerRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Customer\ListCustomerResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Customer\CustomerCreateResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Customer\RemoveCustomerResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Customer\RetrieveNotificationCustomerResponseDTO;

class Customer extends BaseMethods
{

    public function createNewCustomer(CustomerRequestDTO $data): CustomerCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/customers', $data);
        return CustomerCreateResponseDTO::fromArray($response);
    }

    public function listCustomers(ListCustomerRequestDTO $data): ListCustomerResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/customers', $data);
        return ListCustomerResponseDTO::fromArray( $response);
    }

    public function retrieveSingleCustomer(string $id): CustomerCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/customers/{$id}");
        return CustomerCreateResponseDTO::fromArray($response);
    }

    public function updateExistingCustomer(string $id, CustomerRequestDTO $data): CustomerCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::PUT, "/customers/{$id}", $data);
        return CustomerCreateResponseDTO::fromArray($response);
    }

    public function removeCustomer(string $id): RemoveCustomerResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/customers/{$id}");
        return RemoveCustomerResponseDTO::fromArray($response);
    }

    public function restoreRemovedCustomer(string $id): CustomerCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/customers/{$id}/restore");
        return CustomerCreateResponseDTO::fromArray($response);
    }

    public function retrieveNotificationsFromCustomer(string $id): RetrieveNotificationCustomerResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/customers/{$id}/notifications");
        return RetrieveNotificationCustomerResponseDTO::fromArray($response);
    }
}