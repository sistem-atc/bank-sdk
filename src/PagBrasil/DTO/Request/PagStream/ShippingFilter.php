<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use DateTimeInterface;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/** Filtro de GET …/shippings. `status`: pending, completed, canceled. */
final class ShippingFilter implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly ?string $status = null,
        public readonly DateTimeInterface|string|null $from = null,
        public readonly DateTimeInterface|string|null $to = null,
    ) {}
}
