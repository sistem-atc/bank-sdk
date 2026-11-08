<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix\ListKeysRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix\ListKeysResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix\QRCodeStaticResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix\PixAddressKeyResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix\CreateQRCodeStaticRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix\CreatePixAddressKeyRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix\DeleteQrCodeStaticResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix\AvailableTokenBucketCheckResponseDTO;

class Pix extends BaseMethods
{
    public function createKey(CreatePixAddressKeyRequestDTO $data): PixAddressKeyResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/pix/addressKeys', $data);
        return PixAddressKeyResponseDTO::fromArray($response);
    }
    
    public function listKeys(ListKeysRequestDTO $data): ListKeysResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/pix/addressKeys', $data);
        return ListKeysResponseDTO::fromArray($response);
    }
    
    public function retrieveSingleKey(string $id): PixAddressKeyResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/pix/addressKeys/{$id}");
        return PixAddressKeyResponseDTO::fromArray($response);
    }
    
    public function removeKey(string $id): PixAddressKeyResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/pix/addressKeys/{$id}");
        return PixAddressKeyResponseDTO::fromArray($response);
    }
    
    public function createQrCodeStatic(CreateQRCodeStaticRequestDTO $data): QRCodeStaticResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/pix/qrCodes/static', $data);
        return QRCodeStaticResponseDTO::fromArray($response);
    }

    public function removeStaticQRCode(string $id): DeleteQrCodeStaticResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, "/pix/qrCodes/static/{$id}");
        return DeleteQrCodeStaticResponseDTO::fromArray($response);
    }
    
    public function availableTokenBucketCheck(): AvailableTokenBucketCheckResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, 'pix/tokenBucket/addressKey');
        return AvailableTokenBucketCheckResponseDTO::fromArray($response);
    }  
}