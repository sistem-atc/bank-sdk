<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\SaqueTroco;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\SaqueTroco\RemuneracaoList;

/**
 * Consultas de remuneração de Saque Pix do produto Pix Saque e Troco — base
 * `/saque-troco/v1`. Retorna o detalhe da remuneração por conta e período,
 * na forma analítica (por lançamento) e consolidada.
 *
 * `dataLancamento` é o intervalo "YYYY-MM-DD,YYYY-MM-DD"; `idConta` = agência
 * (4) + conta (7) = 11 dígitos; `cnpj` = documento.
 */
final class RemuneracaoMethods extends BaseMethods
{
    private const BASE = '/saque-troco/v1';

    /**
     * Detalhe da remuneração de um Saque Pix por conta e período (analítico).
     * `GET /remuneracao-analiticos`.
     *
     * @param  array<string, mixed>  $filtros  idConta, dataLancamento, cnpj, page, pageSize
     */
    public function analiticos(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/remuneracao-analiticos', query: $filtros);

        return RemuneracaoList::fromArray($data['data'] ?? $data);
    }

    /**
     * Detalhe da remuneração consolidada de um Saque Pix por conta e período.
     * `GET /remuneracao-consolidados`.
     *
     * @param  array<string, mixed>  $filtros  idConta, dataLancamento, cnpj, page, pageSize
     */
    public function consolidados(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/remuneracao-consolidados', query: $filtros);

        return RemuneracaoList::fromArray($data['data'] ?? $data);
    }
}
