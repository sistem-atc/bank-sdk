<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\PixKeyType;

/** Chave Pix do CLIENTE pra estorno (precisa estar habilitado no Dashboard). */
final class RefundPixKey implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[JsonKey('customer_pix_key_type')]
        public readonly PixKeyType $type,
        #[JsonKey('customer_pix_key')]
        public readonly string $key,
    ) {}
}
