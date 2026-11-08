<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment\FileResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment\InstallmentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment\ListInstallmentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment\RefundInstallmentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment\CreateInstallmentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment\ListInstallmentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment\DeleteInstallmentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment\UpdateSplitInstallmentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment\ListPaymentInstallmentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment\ListPaymentInstallmentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment\UpdateInstallmentSplitsResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment\GenerateInstallmentBookletRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment\CancelChargesInstallmentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment\CreateInstallmentWithCreditCardRequestDTO;

class Installment extends BaseMethods
{
    public function createInstallment(CreateInstallmentRequestDTO $data): InstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/installments", $data);
        return InstallmentResponseDTO::fromArray(($response));
    }

    public function listInstallmentsCreateInstallmentWithCreditCard(ListInstallmentRequestDTO $data): ListInstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/installments', $data);
        return ListInstallmentResponseDTO::fromArray($response);
    }

    public function createInstallmentWithCreditCard(CreateInstallmentWithCreditCardRequestDTO $data): InstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/installments", $data);
        return InstallmentResponseDTO::fromArray(($response));
    }

    public function retrieveSingleInstallment(string $id): InstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/installments/{$id}");
        return InstallmentResponseDTO::fromArray(($response));
    }

    public function removeInstallment(string $id): DeleteInstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/installments/{$id}");
        return DeleteInstallmentResponseDTO::fromArray($response);
    }

    public function listPaymentsInstallment(string $id, ListPaymentInstallmentRequestDTO $data): ListPaymentInstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/installments/{$id}/payments", $data);
        return ListPaymentInstallmentResponseDTO::fromArray($response);
    }

    public function generateInstallmentBooklet(string $id, GenerateInstallmentBookletRequestDTO $data): FileResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/installments/{$id}/payments",$data, true);
        return new FileResponseDTO($response);
    }

    public function refundInstallment(string $id, RefundInstallmentRequestDTO $data): InstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/installments/{$id}/refund", $data);
        return InstallmentResponseDTO::fromArray(($response));
    }

    public function updateInstallmentSplits(string $id, UpdateSplitInstallmentRequestDTO $data): UpdateInstallmentSplitsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::PUT, "/installments/{$id}/splits", $data);
        return UpdateInstallmentSplitsResponseDTO::fromArray(($response));
    }

    public function cancelChargesInstallment(string $id): CancelChargesInstallmentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/installments/{$id}/payments");
        return CancelChargesInstallmentResponseDTO::fromArray($response);
    }
}