<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Transfer;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AutomaticPix\TransferResponseDTO;

final class ListTransferResponseDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?bool $hasMore = null,
        public readonly ?int $totalCount = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        #[ArrayOf(TransferResponseDTO::class)] public readonly ?array $data = null,
    ) {}
}