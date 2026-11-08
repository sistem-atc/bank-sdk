# Metodos de AccountDocument

Assinaturas implementadas em `src/Gateways/Asaas/Methods/AccountDocument.php`.

## Indice

- [checkPendingDocuments](#checkpendingdocuments)
- [sendDocuments](#senddocuments)
- [viewDocumentSent](#viewdocumentsent)
- [updateSentDocument](#updatesentdocument)
- [removeSentDocument](#removesentdocument)

## checkPendingDocuments

```php
Asaas::make()->accountDocument($integration)->checkPendingDocuments(): CheckPendingDocumentsResponseDTO
```

## sendDocuments

```php
Asaas::make()->accountDocument($integration)->sendDocuments(
    string $id,
    SendDocumentRequestDTO $multipartData
): SendDocumentsResponseDTO
```

## viewDocumentSent

```php
Asaas::make()->accountDocument($integration)->viewDocumentSent(string $documentId): SendDocumentsResponseDTO
```

## updateSentDocument

```php
Asaas::make()->accountDocument($integration)->updateSentDocument(
    string $documentId,
    SendDocumentRequestDTO $multipartData
): SendDocumentsResponseDTO
```

## removeSentDocument

```php
Asaas::make()->accountDocument($integration)->removeSentDocument(
    string $documentId
): RemoveDocumentsResponseDTO
```

## Referencia

- [Documentacao Oficial - Envio de Documentos](https://docs.asaas.com/docs/envio-de-documentos)

## Como montar os DTOs

### SendDocumentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountDocument\SendDocumentRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\TypePendingDocument;

$dto = SendDocumentRequestDTO::fromArray([
    'filePath' => storage_path('app/documentos/arquivo.pdf'),
    'type' => TypePendingDocument::CPF,
]);
```
