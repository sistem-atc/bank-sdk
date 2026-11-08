<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Bill\BillResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Bill\ListBillResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Bill\CreateBillRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Bill\SimulateBillPaymentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Bill\SimulateBillPaymentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Bill\ListBillPaymentsFilterRequestDTO;

class Bill extends BaseMethods
{

    public function createBill(CreateBillRequestDTO $data): BillResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/bill', $data);
        return BillResponseDTO::fromArray($response);
    }

    public function listBill(ListBillPaymentsFilterRequestDTO $data): ListBillResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/bill', $data);
        return ListBillResponseDTO::fromArray($response);
    }

    public function simulateBillPayment(SimulateBillPaymentRequestDTO $data): SimulateBillPaymentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/bill/simulate", $data);
        return SimulateBillPaymentResponseDTO::fromArray($response);
    }

    public function retrieveSingleBill(string $id): BillResponseDTO
    {
        $response =  $this->makeRequest(HttpMethod::GET, "/bill/{$id}");
        return BillResponseDTO::fromArray($response);
    }

    public function cancelBill(string $id): BillResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/bill/{$id}/cancel");
        return BillResponseDTO::fromArray($response);
    }
}