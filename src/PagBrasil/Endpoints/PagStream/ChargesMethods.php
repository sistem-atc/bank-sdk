<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PagStream;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\PagBrasil\Bases\RestMethods;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ChargeFilter;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\ChargeAttemptList;

/**
 * Cobranças da loja inteira — GET /charges (mais recente primeiro).
 * Filtros: status (pending, success, error), type (credit_card,
 * payment_link, pix_automatico), date_from/date_to (Y-m-d, sobre
 * scheduled_for), subscription_code, order_number, page, per_page (máx. 100).
 */
final class ChargesMethods extends RestMethods
{
    /** @param ChargeFilter|array<string, mixed> $filtros */
    public function listar(ChargeFilter|array $filtros = []): ChargeAttemptList
    {
        return ChargeAttemptList::fromArray($this->request(HttpMethod::GET, '/charges', query: $this->payload($filtros)));
    }
}
