<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListInstallmentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?bool $hasMore = null,
        public readonly ?int $totalCount = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        #[ArrayOf(InstallmentResponseDTO::class)] public readonly ?array $data = null,
    ) {}
}