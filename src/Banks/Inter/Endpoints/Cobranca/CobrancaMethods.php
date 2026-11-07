<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Cobranca;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\CobrancaDetalhadaResponseBody;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\CobrancasResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\EmitirCobrancaAsyncResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\GetStatusUpdateResponseBody;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\SumarioCobranca;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\UpdateCobrancaResponseBody;

/**
 * Cobrança com código de barras e QR Code Pix ("BolePix") — API Cobrança v3
 * (`/cobranca/v3/cobrancas`). Escopos `boleto-cobranca.read` / `.write`.
 *
 * A emissão é ASSÍNCRONA: emitir() devolve só o `codigoSolicitacao`; o boleto
 * e o Pix ficam prontos depois (consultar() ou o webhook de cobrança).
 */
final class CobrancaMethods extends BaseMethods
{
    /**
     * `POST /cobrancas`.
     *
     * @param  array<string, mixed>  $dados  corpo EmitirCobrancaRequestBody (seuNumero, valorNominal, dataVencimento, numDiasAgenda, pagador, desconto, multa, mora, mensagem…)
     */
    public function emitir(array $dados): EmitirCobrancaAsyncResponse
    {
        return EmitirCobrancaAsyncResponse::fromArray($this->makeRequest(HttpMethod::POST, '/cobrancas', body: $dados));
    }

    /** `GET /cobrancas/{codigoSolicitacao}` — cobrança + boleto + Pix + nota fiscal. */
    public function consultar(string $codigoSolicitacao): CobrancaDetalhadaResponseBody
    {
        return CobrancaDetalhadaResponseBody::fromArray(
            $this->makeRequest(HttpMethod::GET, '/cobrancas/'.rawurlencode($codigoSolicitacao)),
        );
    }

    /**
     * `GET /cobrancas` — busca paginada. Período (`Y-m-d`) obrigatório.
     *
     * @param  array{filtrarDataPor?: 'VENCIMENTO'|'EMISSAO'|'PAGAMENTO', situacao?: string, pessoaPagadora?: string, cpfCnpjPessoaPagadora?: string, seuNumero?: string, tipoCobranca?: 'SIMPLES'|'PARCELADO'|'RECORRENTE', itensPorPagina?: int, paginaAtual?: int, ordenarPor?: string, tipoOrdenacao?: 'ASC'|'DESC'}  $filtros
     */
    public function listar(string $dataInicial, string $dataFinal, array $filtros = []): CobrancasResponse
    {
        $data = $this->makeRequest(HttpMethod::GET, '/cobrancas', query: self::query($dataInicial, $dataFinal, $filtros));

        return CobrancasResponse::fromArray($data);
    }

    /**
     * `GET /cobrancas/sumario` — totais por situação no período.
     *
     * @param  array<string, mixed>  $filtros  mesmos filtros de listar(), sem paginação
     * @return list<SumarioCobranca>
     */
    public function sumario(string $dataInicial, string $dataFinal, array $filtros = []): array
    {
        $data = $this->makeRequest(HttpMethod::GET, '/cobrancas/sumario', query: self::query($dataInicial, $dataFinal, $filtros));

        return array_map(static fn (array $item) => SumarioCobranca::fromArray($item), array_values($data));
    }

    /**
     * `PATCH /cobrancas/{codigoSolicitacao}` — altera vencimento, valor,
     * descontos etc. Também assíncrono: acompanhe pelo `codigoEdicao`.
     *
     * @param  array<string, mixed>  $dados  corpo UpdateCobrancaRequestBody
     */
    public function editar(string $codigoSolicitacao, array $dados): UpdateCobrancaResponseBody
    {
        return UpdateCobrancaResponseBody::fromArray(
            $this->makeRequest(HttpMethod::PATCH, '/cobrancas/'.rawurlencode($codigoSolicitacao), body: $dados),
        );
    }

    /** `GET /cobrancas/edicao/{codigoEdicao}` — situação de uma edição. */
    public function consultarEdicao(string $codigoEdicao): GetStatusUpdateResponseBody
    {
        return GetStatusUpdateResponseBody::fromArray(
            $this->makeRequest(HttpMethod::GET, '/cobrancas/edicao/'.rawurlencode($codigoEdicao)),
        );
    }

    /** `GET /cobrancas/{codigoSolicitacao}/pdf` — PDF do boleto em BINÁRIO (decodifica o base64). */
    public function pdf(string $codigoSolicitacao): string
    {
        $data = $this->makeRequest(HttpMethod::GET, '/cobrancas/'.rawurlencode($codigoSolicitacao).'/pdf');

        return (string) base64_decode((string) ($data['pdf'] ?? ''), true);
    }

    /** `POST /cobrancas/{codigoSolicitacao}/cancelar` (HTTP 202). Motivo: até 50 caracteres. */
    public function cancelar(string $codigoSolicitacao, string $motivoCancelamento): void
    {
        $this->makeRequest(
            HttpMethod::POST,
            '/cobrancas/'.rawurlencode($codigoSolicitacao).'/cancelar',
            body: ['motivoCancelamento' => mb_substr($motivoCancelamento, 0, 50)],
        );
    }

    /**
     * `POST /cobrancas/{codigoSolicitacao}/pagar` — SÓ SANDBOX: simula o
     * pagamento da cobrança por boleto ou Pix pra testar o webhook.
     *
     * @param  'BOLETO'|'PIX'  $pagarCom
     */
    public function simularPagamento(string $codigoSolicitacao, string $pagarCom = 'PIX'): void
    {
        $this->makeRequest(
            HttpMethod::POST,
            '/cobrancas/'.rawurlencode($codigoSolicitacao).'/pagar',
            body: ['pagarCom' => $pagarCom],
        );
    }

    /**
     * Paginação do Inter usa chave com ponto (`paginacao.itensPorPagina`).
     *
     * @param  array<string, mixed>  $filtros
     * @return array<string, mixed>
     */
    private static function query(string $dataInicial, string $dataFinal, array $filtros): array
    {
        foreach (['itensPorPagina', 'paginaAtual'] as $chave) {
            if (array_key_exists($chave, $filtros)) {
                $filtros['paginacao.'.$chave] = $filtros[$chave];
                unset($filtros[$chave]);
            }
        }

        return self::filtros(['dataInicial' => $dataInicial, 'dataFinal' => $dataFinal] + $filtros);
    }
}
