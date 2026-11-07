<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Banking;

use RuntimeException;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\DarfResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\EfetuarPagamentoResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\InformacoesPagamento;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\InformacoesPagamentoDarf;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\ObterLoteResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\PagarLoteResponse;

/**
 * Pagamentos da API Banking (`/banking/v2/pagamento`): boleto, DARF e lote.
 * MOVIMENTA dinheiro. Escopos `pagamento-boleto.*`, `pagamento-darf.write` e
 * `pagamento-lote.*`.
 *
 * ⚠️ Diferente do Pix, o pagamento de boleto do Inter NÃO tem chave de
 * idempotência. Antes de repetir um pagamento que falhou por timeout,
 * consulte com listar(['codBarraLinhaDigitavel' => ...]).
 */
final class PagamentosMethods extends BaseMethods implements PaymentsEndpoint
{
    /**
     * Paga um boleto (contrato PaymentsEndpoint) — `POST /pagamento`.
     *
     * Aceita as chaves do contrato comum (snake_case) ou as do Inter
     * (camelCase). O Inter exige `data_vencimento` além do contrato comum.
     *
     * @param  array{linha_digitavel?: string, codigo_barras?: string, valor?: string, data_vencimento?: string, data_pagamento?: string, cpf_cnpj_beneficiario?: string, codBarraLinhaDigitavel?: string, valorPagar?: string, dataVencimento?: string, dataPagamento?: string, cpfCnpjBeneficiario?: string}  $dados
     */
    public function pagarBoleto(array $dados): EfetuarPagamentoResponse
    {
        $body = self::filtros([
            'codBarraLinhaDigitavel' => $dados['codBarraLinhaDigitavel'] ?? $dados['linha_digitavel'] ?? $dados['codigo_barras'] ?? null,
            'valorPagar' => isset($dados['valorPagar']) || isset($dados['valor'])
                ? (string) ($dados['valorPagar'] ?? $dados['valor'])
                : null,
            'dataVencimento' => $dados['dataVencimento'] ?? $dados['data_vencimento'] ?? null,
            'dataPagamento' => $dados['dataPagamento'] ?? $dados['data_pagamento'] ?? null,
            'cpfCnpjBeneficiario' => $dados['cpfCnpjBeneficiario'] ?? $dados['cpf_cnpj_beneficiario'] ?? null,
        ]);

        foreach (['codBarraLinhaDigitavel', 'valorPagar', 'dataVencimento'] as $obrigatorio) {
            if (! isset($body[$obrigatorio])) {
                throw new \InvalidArgumentException("Inter pagarBoleto: campo obrigatório ausente ({$obrigatorio}).");
            }
        }

        $data = $this->makeRequest(HttpMethod::POST, '/pagamento', body: $body);

        return EfetuarPagamentoResponse::fromArray($data);
    }

    /**
     * Consulta um pagamento de boleto pelo `codigoTransacao` devolvido em
     * pagarBoleto() (contrato PaymentsEndpoint).
     */
    public function consultar(string $identificador): DTOInterface
    {
        $lista = $this->listar(['codigoTransacao' => $identificador]);

        return $lista[0] ?? throw new RuntimeException("Inter: pagamento {$identificador} não encontrado.");
    }

    /**
     * `GET /pagamento` — pagamentos de boleto por filtro.
     *
     * @param  array{codBarraLinhaDigitavel?: string, codigoTransacao?: string, dataInicio?: string, dataFim?: string, filtrarDataPor?: 'INCLUSAO'|'VENCIMENTO'|'PAGAMENTO'}  $filtros
     * @return list<InformacoesPagamento>
     */
    public function listar(array $filtros = []): array
    {
        $data = $this->makeRequest(HttpMethod::GET, '/pagamento', query: self::filtros($filtros));

        return array_map(static fn (array $item) => InformacoesPagamento::fromArray($item), array_values($data));
    }

    /** `DELETE /pagamento/{codigoTransacao}` — cancela um pagamento AGENDADO. */
    public function cancelarAgendamento(string $codigoTransacao): void
    {
        $this->makeRequest(HttpMethod::DELETE, '/pagamento/'.rawurlencode($codigoTransacao));
    }

    /**
     * `POST /pagamento/darf`.
     *
     * @param  array<string, mixed>  $dados  corpo DarfRequest do Inter (cnpjCpf, codigoReceita, dataVencimento, valorPrincipal…)
     */
    public function pagarDarf(array $dados): DarfResponse
    {
        return DarfResponse::fromArray($this->makeRequest(HttpMethod::POST, '/pagamento/darf', body: $dados));
    }

    /**
     * `GET /pagamento/darf`.
     *
     * @param  array{codigoSolicitacao?: string, codigoReceita?: string, dataInicio?: string, dataFim?: string}  $filtros
     * @return list<InformacoesPagamentoDarf>
     */
    public function listarDarf(array $filtros = []): array
    {
        $data = $this->makeRequest(HttpMethod::GET, '/pagamento/darf', query: self::filtros($filtros));

        return array_map(static fn (array $item) => InformacoesPagamentoDarf::fromArray($item), array_values($data));
    }

    /**
     * `POST /pagamento/lote` — lote de boletos e/ou DARFs, processado de
     * forma assíncrona (HTTP 202). Acompanhe com consultarLote().
     *
     * @param  array{meuIdentificador?: string, pagamentos: list<array<string, mixed>>}  $dados
     */
    public function pagarLote(array $dados): PagarLoteResponse
    {
        return PagarLoteResponse::fromArray($this->makeRequest(HttpMethod::POST, '/pagamento/lote', body: $dados));
    }

    /** `GET /pagamento/lote/{idLote}`. */
    public function consultarLote(string $idLote): ObterLoteResponse
    {
        return ObterLoteResponse::fromArray($this->makeRequest(HttpMethod::GET, '/pagamento/lote/'.rawurlencode($idLote)));
    }
}
