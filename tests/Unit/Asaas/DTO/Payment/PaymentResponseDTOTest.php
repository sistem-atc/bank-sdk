<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Payment;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment\PaymentResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPayment;
use SistemAtc\Banks\Tests\TestCase;

class PaymentResponseDTOTest extends TestCase
{
    public function test_can_create_from_array(): void
    {
        $data = asaasFixture('Payment/create_payment_response');        
        $dto = PaymentResponseDTO::fromArray($data);
        
        expect($dto)->toBeInstanceOf(PaymentResponseDTO::class)
            ->and($dto->id)->toBe('pay_080225913252')
            ->and($dto->value)->toBe(129.9)
            ->and($dto->status)->toBe(StatusPayment::PENDING);
    }
    
    public function test_can_convert_to_array(): void
    {
    
        $data = asaasFixture('Payment/create_payment_response');
        $dto = PaymentResponseDTO::fromArray($data);
        
        $array = $dto->toArray();
        
        expect($array)->toBeArray()
            ->and($array['id'])->toBe('pay_080225913252');
    }
    
    public function test_with_fixture_data(): void
    {
        $data = asaasFixture('Payment/create_payment_response');
        $dto = PaymentResponseDTO::fromArray($data);
        
        expect($dto)->toBeInstanceOf(PaymentResponseDTO::class)
            ->and($dto->id)->not()->toBeNull();
    }
}
