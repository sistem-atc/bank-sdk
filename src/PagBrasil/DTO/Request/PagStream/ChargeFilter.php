<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use DateTimeInterface;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Filtro de GET /charges. `status`: pending, success, error; `type`:
 * credit_card, payment_link, pix_automatico. Datas sobre a data AGENDADA.
 */
final class ChargeFilter implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $type = null,
        public readonly DateTimeInterface|string|null $dateFrom = null,
        public readonly DateTimeInterface|string|null $dateTo = null,
        public readonly ?string $subscriptionCode = null,
        public readonly ?string $orderNumber = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
    ) {}
}
