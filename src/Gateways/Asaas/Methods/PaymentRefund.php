<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentRefund\RefundBankSlipResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentRefund\RetrieveSinglePaymentResponseDTO;

class PaymentRefund extends BaseMethods
{
    public function retrieveRefundsSinglePayment(string $id): RetrieveSinglePaymentResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/payments/{$id}/refunds");
        return RetrieveSinglePaymentResponseDTO::fromArray($response);
    }
    
    public function refundBankSlip(string $id): RefundBankSlipResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/payments/{$id}/bankSlip/refund");
        return RefundBankSlipResponseDTO::fromArray($response);
    }   
}