<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Payment;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment\PaymentResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class PaymentDTOTest extends TestCase
{
    public function test_create_payment_dto_from_array(): void
    {
        $data = asaasFixture('Payment/create_payment_request');

        $dto = PaymentResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(PaymentResponseDTO::class);
    }

    public function test_payment_dto_to_array(): void
    {
        $data = asaasFixture('Payment/create_payment_request');
        $dto = PaymentResponseDTO::fromArray($data);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
    
    public function test_payment_dto_with_fixture(): void
    {
        $data = asaasFixture("Payment/create_payment_request");

        $dto = PaymentResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(PaymentResponseDTO::class);
    }    

    public function test_payment_dto_validation(): void
    {
        $data = asaasFixture("Payment/create_payment_request");
        
        $dto = PaymentResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(PaymentResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}