<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Statement;

use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Statement\StatementEntry;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;

/**
 * Extrato de conta Itau (Cash Management) pra conciliação.
 * Path/formato a confirmar com a spec real — arquitetura já pronta.
 */
final class StatementMethods extends BaseMethods implements StatementEndpoint
{
    private const PATH = '/extrato/v1/lancamentos';

    /** @return array<int, StatementEntry> */
    public function periodo(string $de, string $ate): array
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH, query: [
            'dataInicio' => $de,
            'dataFim' => $ate,
        ]);

        $itens = $data['lancamentos'] ?? $data['data'] ?? [];

        return array_map(
            static fn (array $item): StatementEntry => StatementEntry::fromArray($item),
            array_values($itens),
        );
    }
}
