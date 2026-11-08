<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Customer;

use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Customer\ListCustomerRequestDTO;
use SistemAtc\Banks\Tests\TestCase;

class ListCustomerTest extends TestCase
{
    public function test_create_list_customer_from_array(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 100,
        ];

        $dto = ListCustomerRequestDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(ListCustomerRequestDTO::class);
    }

    public function test_list_customer_to_array(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 100,
        ];

        $dto = ListCustomerRequestDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_list_customer_validation(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 100,
        ];

        $dto = ListCustomerRequestDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(ListCustomerRequestDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}