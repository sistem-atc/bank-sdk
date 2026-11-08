<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction\PayQrCodeRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PixTransaction\PayQrCodeResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction\DecodeQrCodeRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PixTransaction\DecodeQrCodeResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction\ListTransactionsRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\PixTransaction\ListTransactionResponseDTO;

class PixTransaction extends BaseMethods
{
    public function payQRCode(PayQrCodeRequestDTO $data): PayQrCodeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/pix/qrCodes/pay', $data);
        return PayQrCodeResponseDTO::fromArray($response);
    }
    
    public function decodeQRCodePayment(DecodeQrCodeRequestDTO $data): DecodeQrCodeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/pix/qrCodes/decode', $data);
        return DecodeQrCodeResponseDTO::fromArray($response);
    }
    
    public function retrieveSingleTransaction(string $id): PayQrCodeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/pix/transactions/{$id}");
        return PayQrCodeResponseDTO::fromArray($response);
    }
    
    public function listTransactions(ListTransactionsRequestDTO $data): ListTransactionResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/pix/transactions', $data);
        return ListTransactionResponseDTO::fromArray($response);
    }
    
    public function cancelScheduledTransaction(string $id): PayQrCodeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/pix/transactions/{$id}/cancel");
        return PayQrCodeResponseDTO::fromArray($response);
    }
}