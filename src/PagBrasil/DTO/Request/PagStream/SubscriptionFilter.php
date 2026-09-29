<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;

/**
 * Filtro de GET /subscriptions — a listagem é sempre de UM cliente: CPF/CNPJ
 * e/ou e-mail (os dois juntos = E, não OU). perPage até 100.
 */
final class SubscriptionFilter implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly ?string $taxId = null,
        public readonly ?string $email = null,
        public readonly ?SubscriptionStatus $status = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
    ) {
        if (($taxId === null || $taxId === '') && ($email === null || $email === '')) {
            throw new InvalidArgumentException('PagBrasil: informe taxId e/ou email do cliente.');
        }

        if ($perPage !== null && ($perPage < 1 || $perPage > 100)) {
            throw new InvalidArgumentException('PagBrasil: perPage de 1 a 100.');
        }
    }
}
