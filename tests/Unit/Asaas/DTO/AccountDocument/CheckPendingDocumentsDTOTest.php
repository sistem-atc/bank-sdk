<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AccountDocument;

use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountDocument\CheckPendingDocumentsResponseDTO;
use SistemAtc\Banks\Tests\TestCase;

class CheckPendingDocumentsDTOTest extends TestCase
{
    public function test_create_check_pending_documents_dto_from_array(): void
    {
        $data = [
            'documents' => [],
        ];

        $dto = CheckPendingDocumentsResponseDTO::fromArray($data);

        expect($dto)->toBeInstanceOf(CheckPendingDocumentsResponseDTO::class);
    }

    public function test_check_pending_documents_dto_to_array(): void
    {
        $data = [
            'documents' => [],
        ];

        $dto = CheckPendingDocumentsResponseDTO::fromArray($data);
        $result = $dto->toArray();

        expect($result)->toBeArray();
    }
    
    public function test_check_pending_documents_dto_validation(): void
    {
        $data = [
            'documents' => [],
        ];

        $dto = CheckPendingDocumentsResponseDTO::fromArray($data);
        expect($dto)->toBeInstanceOf(CheckPendingDocumentsResponseDTO::class);

        $result = $dto->toArray();
        expect($result)->toBeArray();
    }
}