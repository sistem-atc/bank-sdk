<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Customer;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Customer\CustomerCreateResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class CustomerCreateDTOTest extends TestCase
{
    public function test_create_customer_create_dto_from_array(): void
    {
        $data = [
            'id' => 'cus_123456',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpfCnpj' => '24971563792',
        ];

        $dto = CustomerCreateResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(CustomerCreateResponseDTO::class);
    }

    public function test_customer_create_dto_to_array(): void
    {
        $data = [
            'id' => 'cus_123456',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpfCnpj' => '24971563792',
        ];

        $dto = CustomerCreateResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_customer_create_dto_with_fixture(): void
    {
        $data = asaasFixture("Customer/create_customer_response");

        $dto = CustomerCreateResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(CustomerCreateResponseDTO::class);
    }    
    public function test_customer_create_dto_validation(): void
    {
        $data = [
            'id' => 'cus_123456',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpfCnpj' => '24971563792',
        ];

        $dto = CustomerCreateResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(CustomerCreateResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}