<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo\RetrieveBusinessDataResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class RetrieveBusinessDataDTOTest extends TestCase
{
    public function test_create_retrieve_business_data_dto_from_array(): void
    {
        $data = [
            'id' => 'acc_123456',
            'companyName' => 'Test Company',
            'companyType' => 'INDIVIDUAL',
        ];

        $dto = RetrieveBusinessDataResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(RetrieveBusinessDataResponseDTO::class);
    }

    public function test_retrieve_business_data_dto_to_array(): void
    {
        $data = [
            'id' => 'acc_123456',
            'companyName' => 'Test Company',
            'companyType' => 'INDIVIDUAL',
        ];

        $dto = RetrieveBusinessDataResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_retrieve_business_data_dto_validation(): void
    {
        $data = [
            'id' => 'acc_123456',
            'companyName' => 'Test Company',
            'companyType' => 'INDIVIDUAL',
        ];

        $dto = RetrieveBusinessDataResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(RetrieveBusinessDataResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}