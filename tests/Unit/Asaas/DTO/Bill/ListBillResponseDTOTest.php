<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Bill;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Bill\ListBillResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class ListBillResponseDTOTest extends TestCase
{
    public function test_create_list_bill_response_dto_from_array(): void
    {
        $data = [
            'data' => [],
        ];

        $dto = ListBillResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(ListBillResponseDTO::class);
    }

    public function test_list_bill_response_dto_to_array(): void
    {
        $data = [
            'data' => [],
        ];

        $dto = ListBillResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_list_bill_response_dto_validation(): void
    {
        $data = [
            'data' => [],
        ];

        $dto = ListBillResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(ListBillResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}