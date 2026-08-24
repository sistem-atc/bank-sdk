<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Conciliacao;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Conciliacao\LancamentoPix;
use SistemAtc\Banks\Itau\DTO\Response\Conciliacao\LancamentosPixList;

/**
 * API Conciliação Pix do Itaú (produto `api_conciliacao_v1`) — base
 * `/conciliacao/v1`. Lista os lançamentos Pix (créditos/débitos) já conciliados
 * da conta, por período/conta/txid/e2eid, para bater o extrato com os
 * recebimentos e pagamentos Pix. Só LÊ.
 *
 * Roda no mesmo host do Recebimentos Pix (`pix_recebimentos`,
 * pix-pj.api.itau.com).
 */
final class ConciliacaoMethods extends BaseMethods
{
    private const BASE = '/conciliacao/v1/lancamentos-pix';

    /**
     * Lista os lançamentos Pix conciliados (paginado). Filtros comuns:
     * `id_conta`, `data_lancamento` ("YYYY-MM-DD,YYYY-MM-DD"), `txid`, `e2eid`,
     * `tipo_lancamento`, `tipo_operacao`, `page`, `page_size`.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function lancamentos(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        // A resposta embrulha a lista direto em `data` (array numérico).
        return LancamentosPixList::fromArray([
            'itens' => $data['data'] ?? [],
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
        ]);
    }

    /**
     * Consulta um lançamento Pix específico —
     * GET /conciliacao/v1/lancamentos-pix/{lancamentoPixId}.
     */
    public function lancamento(string $lancamentoPixId): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($lancamentoPixId));

        return LancamentoPix::fromArray($data['data'] ?? $data);
    }
}
