<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Anticipation;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation\RetrieveAnticipationLimitsResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class RetrieveAntecipationLimitsDTOTest extends TestCase
{
    public function test_create_retrieve_antecipation_limits_dto_from_array(): void
    {
        $data = [
            'availableAnticipationAmount' => 5000,
        ];

        $dto = RetrieveAnticipationLimitsResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(RetrieveAnticipationLimitsResponseDTO::class);
    }

    public function test_retrieve_antecipation_limits_dto_to_array(): void
    {
        $data = [
            'availableAnticipationAmount' => 5000,
        ];

        $dto = RetrieveAnticipationLimitsResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_retrieve_antecipation_limits_dto_with_fixture(): void
    {
        $data = asaasFixture("Anticipation/retrieve_anticipation_limits_response");

        $dto = RetrieveAnticipationLimitsResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(RetrieveAnticipationLimitsResponseDTO::class);
    }    
    public function test_retrieve_antecipation_limits_dto_validation(): void
    {
        $data = [
            'availableAnticipationAmount' => 5000,
        ];

        $dto = RetrieveAnticipationLimitsResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(RetrieveAnticipationLimitsResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}