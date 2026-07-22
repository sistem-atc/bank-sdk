<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts\Endpoints;

/**
 * Extrato de conta corrente (Cash Management) pra conciliação bancária.
 * Interface comum a todos os bancos.
 */
interface StatementEndpoint
{
    /**
     * Lançamentos da conta no período. Datas em `Y-m-d`.
     *
     * @return array<int, \SistemAtc\Banks\Contracts\DTOInterface>
     */
    public function periodo(string $de, string $ate): array;
}
