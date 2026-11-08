<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AutomaticPix;

use SistemAtc\Banks\Tests\TestCase;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPixPayment;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Authorization;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AutomaticPix\SinglePaymentResponseDTO;

class SinglePaymentResponseDTOTest extends TestCase
{
    public function test_create_single_payment_response_dto_from_array(): void
    {
        $data = [
            'id' => 'pip_123',
            'endToEndIdentifier' => true,
            'authorization' => [
                'id' => 'aut_123',
                'customerId' => 'cus_123',
            ],
            'status' => 'SCHEDULED',
            'paymentId' => 'pay_123',
        ];

        $dto = SinglePaymentResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(SinglePaymentResponseDTO::class)
            ->and($dto->status)->toBe(StatusPixPayment::SCHEDULED)
            ->and($dto->authorization)->toBeInstanceOf(Authorization::class);
    }

    public function test_single_payment_response_dto_to_array(): void
    {
        $data = [
            'id' => 'pip_123',
            'endToEndIdentifier' => true,
            'status' => 'SCHEDULED',
            'paymentId' => 'pay_123',
        ];

        $dto = SinglePaymentResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray()
            ->and($result['status'])->toBe('SCHEDULED');
    }
}
