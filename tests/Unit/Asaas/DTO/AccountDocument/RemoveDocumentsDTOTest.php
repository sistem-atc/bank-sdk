<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AccountDocument;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountDocument\RemoveDocumentsResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class RemoveDocumentsDTOTest extends TestCase
{
    public function test_create_remove_documents_dto_from_array(): void
    {
        $data = [
            'deleted' => true,
            'id' => 'uniqueID',
        ];

        $dto = RemoveDocumentsResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(RemoveDocumentsResponseDTO::class);
    }

    public function test_remove_documents_dto_to_array(): void
    {
        $data = [
            'deleted' => true,
            'id' => 'uniqueID',
        ];

        $dto = RemoveDocumentsResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_remove_documents_dto_validation(): void
    {
        $data = [
            'deleted' => true,
            'id' => 'uniqueID',
        ];

        $dto = RemoveDocumentsResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(RemoveDocumentsResponseDTO::class);
        
        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}