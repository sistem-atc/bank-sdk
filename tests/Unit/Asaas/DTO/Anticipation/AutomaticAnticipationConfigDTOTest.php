<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\Anticipation;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation\AutomaticAnticipationConfigResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class AutomaticAnticipationConfigDTOTest extends TestCase
{
    public function test_create_automatic_anticipation_config_dto_from_array(): void
    {
        $data = asaasFixture('Anticipation/retrieve_status_of_automatic_anticipation_response');
        $dto = AutomaticAnticipationConfigResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(AutomaticAnticipationConfigResponseDTO::class);
    }

    public function test_automatic_anticipation_config_dto_to_array(): void
    {
        $data = asaasFixture('Anticipation/retrieve_status_of_automatic_anticipation_response');
        $dto = AutomaticAnticipationConfigResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_automatic_anticipation_config_dto_validation(): void
    {
        $data = asaasFixture('Anticipation/retrieve_status_of_automatic_anticipation_response');
        $dto = AutomaticAnticipationConfigResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(AutomaticAnticipationConfigResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}