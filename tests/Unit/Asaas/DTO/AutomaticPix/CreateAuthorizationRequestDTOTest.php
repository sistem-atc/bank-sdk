<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AutomaticPix;

use SistemAtc\Banks\Gateways\Asaas\Enums\Frequency;
use SistemAtc\Banks\Tests\TestCase;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\ImmediateQrCode;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix\CreateAuthorizationRequestDTO;

class CreateAuthorizationRequestDTOTest extends TestCase
{
    public function test_create_create_authorization_request_dto_from_array(): void
    {
        $data = [
            'frequency' => 'MONTHLY',
            'contractId' => 'contract_123',
            'startDate' => '2026-02-01',
            'customerId' => 'cus_123',
            'immediateQrCode' => [
                'expirationSeconds' => 600,
                'originalValue' => 120.50,
            ],
        ];

        $dto = CreateAuthorizationRequestDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(CreateAuthorizationRequestDTO::class)
            ->and($dto->frequency)->toBe(Frequency::MONTHLY)
            ->and($dto->immediateQrCode)->toBeInstanceOf(ImmediateQrCode::class);
    }

    public function test_create_authorization_request_dto_to_array(): void
    {
        $data = [
            'frequency' => 'MONTHLY',
            'contractId' => 'contract_123',
            'startDate' => '2026-02-01',
            'customerId' => 'cus_123',
            'immediateQrCode' => [
                'expirationSeconds' => 600,
                'originalValue' => 120.50,
            ],
        ];

        $dto = CreateAuthorizationRequestDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray()
            ->and($result['frequency'])->toBe('MONTHLY');
    }
}
