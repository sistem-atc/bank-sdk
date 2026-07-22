<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Boletos;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\ExtratoResumidoResponse;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\MovimentacaoExtratoResponse;

/**
 * Boletos Cobrança — EXTRATO de movimentações.
 * Produto do portal: "API Boletos - Extrato Boleto Cobrança".
 * Base path: `/extrato/v1/francesas`. Scope: `boletoscash-francesas.read`.
 *
 * ATENÇÃO (wiring): em produção esta API vive em host próprio
 * `boleto.api.itau.com`, DIFERENTE do host padrão de cash_management. O
 * connector precisa apontar o base_url pra esse host ao instanciar esta classe.
 */
final class BoletosExtratoMethods extends BaseMethods
{
    private const BASE = '/extrato/v1/francesas';

    /**
     * Calendário de movimentações: datas que têm movimentação no mês de
     * referência. Query: agencia, conta, dac, mesReferencia (MMAAAA).
     *
     * @param  array<string, mixed>  $filtros
     */
    public function calendarioMovimentacoes(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return ExtratoResumidoResponse::fromArray($data);
    }

    /**
     * Extrato RESUMIDO de movimentações (cobrança/desconto/tarifação) numa data.
     * Query mínima: data (AAAA-MM-DD).
     *
     * @param  array<string, mixed>  $filtros
     */
    public function movimentacoesResumidas(string $francesaId, array $filtros): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/'.rawurlencode($francesaId).'/movimentacoes-resumidas',
            query: $filtros,
        );

        return ExtratoResumidoResponse::fromArray($data);
    }

    /**
     * Extrato DETALHADO de movimentações numa data (paginado).
     * Query: data (AAAA-MM-DD), page, pageSize e, opcionalmente, nossoNumero /
     * numeroCarteira.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function movimentacoes(string $francesaId, array $filtros): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/'.rawurlencode($francesaId).'/movimentacoes',
            query: $filtros,
        );

        return MovimentacaoExtratoResponse::fromArray($data);
    }
}
