<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Payment;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment\ListPaymentResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class ListPaymentDTOTest extends TestCase
{
    public function test_create_list_payment_dto_from_array(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 50,
        ];

        $dto = ListPaymentResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(ListPaymentResponseDTO::class);
    }

    public function test_list_payment_dto_to_array(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 50,
        ];

        $dto = ListPaymentResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_list_payment_dto_validation(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 50,
        ];

        $dto = ListPaymentResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(ListPaymentResponseDTO::class);

        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}