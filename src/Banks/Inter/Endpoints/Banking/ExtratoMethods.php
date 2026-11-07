<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Banking;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\ListaTransacoes;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\ListaTransacoesCompletaPadrao;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\ListaTransacoesCompletaScroll;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\Saldo;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\TransacaoSimples;

/**
 * Extrato e saldo da conta PJ — API Banking (`/banking/v2`). Escopo
 * `extrato.read`. Só LÊ, não movimenta dinheiro.
 */
final class ExtratoMethods extends BaseMethods implements StatementEndpoint
{
    /**
     * Lançamentos no período (contrato StatementEndpoint). Datas `Y-m-d`.
     *
     * @return list<TransacaoSimples>
     */
    public function periodo(string $de, string $ate): array
    {
        return $this->extrato($de, $ate)->transacoes;
    }

    /** `GET /extrato` — lista simples de transações do período. */
    public function extrato(string $de, string $ate): ListaTransacoes
    {
        $data = $this->makeRequest(HttpMethod::GET, '/extrato', query: [
            'dataInicio' => $de,
            'dataFim' => $ate,
        ]);

        return ListaTransacoes::fromArray($data);
    }

    /**
     * `GET /extrato/completo` — transações com detalhe por tipo (Pix, boleto,
     * tarifa…), paginado. Com `scrollEnabled` o Inter troca a paginação por
     * cursor (`scrollId`), e a resposta vira ListaTransacoesCompletaScroll.
     *
     * @param  array{pagina?: int, tamanhoPagina?: int, tipoOperacao?: 'C'|'D', tipoTransacao?: string, scrollEnabled?: bool, scrollId?: string}  $filtros
     */
    public function extratoCompleto(string $de, string $ate, array $filtros = []): ListaTransacoesCompletaPadrao|ListaTransacoesCompletaScroll
    {
        if (isset($filtros['scrollEnabled'])) {
            $filtros['scrollEnabled'] = $filtros['scrollEnabled'] ? 'true' : 'false';
        }

        $data = $this->makeRequest(HttpMethod::GET, '/extrato/completo', query: self::filtros([
            'dataInicio' => $de,
            'dataFim' => $ate,
        ] + $filtros));

        return array_key_exists('scrollId', $data) || ($filtros['scrollEnabled'] ?? 'false') === 'true'
            ? ListaTransacoesCompletaScroll::fromArray($data)
            : ListaTransacoesCompletaPadrao::fromArray($data);
    }

    /** `GET /extrato/exportar` — PDF do extrato, devolvido em BINÁRIO (decodifica o base64). */
    public function exportarPdf(string $de, string $ate): string
    {
        $data = $this->makeRequest(HttpMethod::GET, '/extrato/exportar', query: [
            'dataInicio' => $de,
            'dataFim' => $ate,
        ]);

        return (string) base64_decode((string) ($data['pdf'] ?? ''), true);
    }

    /**
     * `GET /saldo`. Sem `dataSaldo`, devolve o saldo atual com bloqueios e
     * limite; com ela (`Y-m-d`), o saldo daquele dia (só `disponivel`).
     */
    public function saldo(?string $dataSaldo = null): Saldo
    {
        $data = $this->makeRequest(HttpMethod::GET, '/saldo', query: self::filtros(['dataSaldo' => $dataSaldo]));

        return Saldo::fromArray($data);
    }
}
