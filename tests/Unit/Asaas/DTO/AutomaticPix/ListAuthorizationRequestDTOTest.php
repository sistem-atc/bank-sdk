<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AutomaticPix;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPix;
use SistemAtc\Banks\Tests\TestCase;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix\ListAuthorizationRequestDTO;

class ListAuthorizationRequestDTOTest extends TestCase
{
    public function test_create_list_authorization_request_dto_from_array(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 50,
            'status' => 'ACTIVE',
            'customerId' => 'cus_123',
        ];

        $dto = ListAuthorizationRequestDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(ListAuthorizationRequestDTO::class)
            ->and($dto->status)->toBe(StatusPix::ACTIVE);
    }

    public function test_list_authorization_request_dto_to_array(): void
    {
        $data = [
            'offset' => 0,
            'limit' => 50,
            'status' => 'ACTIVE',
            'customerId' => 'cus_123',
        ];

        $dto = ListAuthorizationRequestDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray()
            ->and($result['status'])->toBe('ACTIVE');
    }
}
