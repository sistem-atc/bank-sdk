<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Dda;

use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Dda\DdaBoleto;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\Endpoints\DdaEndpoint;

/**
 * DDA Itau — consulta de boletos registrados contra o CNPJ da empresa.
 *
 * ATENÇÃO: o path abaixo é a ESTRUTURA da chamada; o endpoint real e o formato
 * de resposta devem ser confirmados no portal Itau (API DDA / Boletos)
 * antes do go-live. A arquitetura (autenticação, retry, DTO) já está pronta —
 * só o mapa path/campos é que fecha com a spec real.
 */
final class DdaMethods extends BaseMethods implements DdaEndpoint
{
    private const PATH = '/dda/v1/boletos';

    /**
     * @param  array{vencimento_de?: string, vencimento_ate?: string, situacao?: string}  $filtros
     * @return array<int, DdaBoleto>
     */
    public function consultar(array $filtros = []): array
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH, query: $filtros);

        $itens = $data['boletos'] ?? $data['data'] ?? [];

        return array_map(
            static fn (array $item): DdaBoleto => DdaBoleto::fromArray($item),
            array_values($itens),
        );
    }
}
