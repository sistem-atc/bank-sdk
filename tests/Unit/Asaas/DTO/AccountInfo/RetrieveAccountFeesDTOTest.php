<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo\RetrieveAccountFeesResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class RetrieveAccountFeesDTOTest extends TestCase
{
    public function test_create_retrieve_account_fees_dto_from_array(): void
    {
        $data = [
            'fees' => [],
        ];

        $dto = RetrieveAccountFeesResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(RetrieveAccountFeesResponseDTO::class);
    }

    public function test_retrieve_account_fees_dto_to_array(): void
    {
        $data = [
            'fees' => [],
        ];

        $dto = RetrieveAccountFeesResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_retrieve_account_fees_dto_validation(): void
    {
        $data = [
            'fees' => [],
        ];

        $dto = RetrieveAccountFeesResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(RetrieveAccountFeesResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}