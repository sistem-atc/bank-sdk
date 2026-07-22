<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Boletos;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Itau\Bases\BaseMethods;

/**
 * Boletos Cobrança — INSTRUÇÕES e ALTERAÇÕES sobre um boleto já registrado.
 * Produto do portal: "API Boletos - Emissão e Instrução".
 * Base path: `/cash_management/v2/boletos/{idBoleto}/{instrucao}` (PATCH).
 *
 * Todos os endpoints respondem 204 (sem corpo) no sucesso — por isso os métodos
 * retornam void; um erro vira BankRequestException na base. `idBoleto` é
 * Agência(4)+Conta(7)+DAC(1)+Carteira(3)+NossoNúmero(8|16).
 *
 * As instruções só ficam disponíveis 1 dia útil após a emissão.
 */
final class BoletosInstrucaoMethods extends BaseMethods
{
    private const BASE = '/cash_management/v2/boletos';

    /**
     * PATCH genérico de instrução: monta o path e envia o body.
     *
     * @param  array<string, mixed>  $body
     */
    private function instruir(string $idBoleto, string $instrucao, array $body = []): void
    {
        $this->makeRequest(
            HttpMethod::PATCH,
            self::BASE.'/'.rawurlencode($idBoleto).'/'.$instrucao,
            body: $body,
        );
    }

    /** Baixa (cancela) o boleto. Body vazio. */
    public function baixar(string $idBoleto): void
    {
        $this->instruir($idBoleto, 'baixa');
    }

    /** Altera o valor nominal do título. Ex.: "500.00". */
    public function alterarValorNominal(string $idBoleto, string $valorTitulo): void
    {
        $this->instruir($idBoleto, 'valor_nominal', ['valor_titulo' => $valorTitulo]);
    }

    /** Altera a data de vencimento (AAAA-MM-DD). */
    public function alterarDataVencimento(string $idBoleto, string $dataVencimento): void
    {
        $this->instruir($idBoleto, 'data_vencimento', ['data_vencimento' => $dataVencimento]);
    }

    /** Altera a data limite de pagamento (AAAA-MM-DD). */
    public function alterarDataLimitePagamento(string $idBoleto, string $dataLimitePagamento): void
    {
        $this->instruir($idBoleto, 'data_limite_pagamento', ['data_limite_pagamento' => $dataLimitePagamento]);
    }

    /** Concede/altera abatimento. Ex.: "10.00". */
    public function alterarAbatimento(string $idBoleto, string $valorAbatimento): void
    {
        $this->instruir($idBoleto, 'abatimento', ['valor_abatimento' => $valorAbatimento]);
    }

    /** Altera o "seu número" (controle do cliente, até 10 caracteres). */
    public function alterarSeuNumero(string $idBoleto, string $textoSeuNumero): void
    {
        $this->instruir($idBoleto, 'seu_numero', ['texto_seu_numero' => $textoSeuNumero]);
    }

    /**
     * Instrução de juros. Body: codigo_tipo_juros + valor/percentual + data.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarJuros(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'juros', $dados);
    }

    /**
     * Instrução de multa. Body: codigo_tipo_multa + valor/percentual + data.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarMulta(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'multa', $dados);
    }

    /**
     * Instrução de desconto. Body: codigo_tipo_desconto + data/valor/percentual.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarDesconto(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'desconto', $dados);
    }

    /**
     * Instrução de protesto. Body: protesto (bool) + quantidade_dias_protesto.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarProtesto(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'protesto', $dados);
    }

    /**
     * Instrução de negativação. Body: negativacao { codigo_tipo_negativacao }.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarNegativacao(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'negativacao', $dados);
    }

    /**
     * Altera dados do pagador. Body: bloco pagador (pessoa/endereço).
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarPagador(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'pagador', $dados);
    }

    /**
     * Altera as regras de recebimento divergente.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarRecebimentoDivergente(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'recebimento_divergente', $dados);
    }

    /**
     * Altera/inclui o sacador avalista.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterarSacadorAvalista(string $idBoleto, array $dados): void
    {
        $this->instruir($idBoleto, 'sacador_avalista', $dados);
    }
}
