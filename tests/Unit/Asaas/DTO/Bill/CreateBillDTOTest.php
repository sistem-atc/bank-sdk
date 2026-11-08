<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Bill;

use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Bill\CreateBillRequestDTO;
use SistemAtc\Banks\Tests\TestCase;

class CreateBillDTOTest extends TestCase
{
    public function test_create_create_bill_dto_from_array(): void
    {
        $data = [
            'identificationField' => 'Id123456',
            'value' => 100,
            'description' => 'Test Bill',
            'dueDate' => '2025-02-20',
        ];

        $dto = CreateBillRequestDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(CreateBillRequestDTO::class);
    }

    public function test_create_bill_dto_to_array(): void
    {
        $data = [
            'identificationField' => 'Id123456',
            'value' => 100,
            'description' => 'Test Bill',
            'dueDate' => '2025-02-20',
        ];

        $dto = CreateBillRequestDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_create_bill_dto_validation(): void
    {
        $data = [
            'identificationField' => 'Id123456',
            'value' => 100,
            'description' => 'Test Bill',
            'dueDate' => '2025-02-20',
        ];

        $dto = CreateBillRequestDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(CreateBillRequestDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}