<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo\UpdateBusinessDataRequestDTO;
use SistemAtc\Banks\Tests\TestCase;

class UpdateBusinessDataDTOTest extends TestCase
{
    public function test_create_update_business_data_dto_from_array(): void
    {
        $data = [
            'companyName' => 'Test Company',
            'companyType' => 'INDIVIDUAL',
        ];

        $dto = UpdateBusinessDataRequestDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(UpdateBusinessDataRequestDTO::class);
    }

    public function test_update_business_data_dto_to_array(): void
    {
        $data = [
            'companyName' => 'Test Company',
            'companyType' => 'INDIVIDUAL',
        ];

        $dto = UpdateBusinessDataRequestDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_update_business_data_dto_validation(): void
    {
        $data = [
            'companyName' => 'Test Company',
            'companyType' => 'INDIVIDUAL',
        ];

        $dto = UpdateBusinessDataRequestDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(UpdateBusinessDataRequestDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}