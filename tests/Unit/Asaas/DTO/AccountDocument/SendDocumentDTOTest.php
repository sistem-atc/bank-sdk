<?php

namespace SistemAtc\Banks\Tests\Unit\Asaas\DTO\AccountDocument;

use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountDocument\SendDocumentRequestDTO;
use SistemAtc\Banks\Tests\TestCase;

class SendDocumentDTOTest extends TestCase
{
    public function test_can_create_with_multipart_file(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, 'test content');
        
        try {
            $dto = new SendDocumentRequestDTO(
                filePath: $filePath,
                type: null,
            );

            expect($dto)->toBeInstanceOf(SendDocumentRequestDTO::class)
                ->and($dto->filePath)->toBe($filePath);
        } finally {
            unlink($filePath);
        }
    }

    public function test_throws_exception_for_missing_file(): void
    {
        expect(function () {
            new SendDocumentRequestDTO(
                filePath: '/nonexistent/file.pdf',
                type: null,
            );
        })->toThrow(\InvalidArgumentException::class);
    }
    
    public function test_can_convert_to_multipart(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, 'test content');
        
        try {
            $dto = new SendDocumentRequestDTO(
                filePath: $filePath,
                type: null,
            );
            
            $result = $dto->toMultipart();
            
            expect($result)->toBeArray();
        } finally {
            unlink($filePath);
        }
    }
}