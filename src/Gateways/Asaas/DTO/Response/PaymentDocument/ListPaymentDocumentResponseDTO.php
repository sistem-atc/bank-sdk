<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDocument;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListPaymentDocumentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $object,
        public readonly ?bool $hasMore,
        public readonly ?int $totalCount,
        public readonly ?int $limit,
        public readonly ?int $offset,
        #[ArrayOf(PaymentDocumentResponseDTO::class)] public readonly ?array $data,
    ) {}
}