<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\SaldoExtrato;

use DateTimeInterface;
use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato\ExtratoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato\Lancamento;
use SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato\SaldoResponse;
use RuntimeException;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;

/**
 * Saldo e extrato de contas PJ do Bradesco — base da conciliação bancária.
 *
 * Família: open_api (host openapi.bradesco.com.br).
 * Cada operação tem o SEU microserviço/base path:
 *  - GET /v1/fornecimento-extratos-contas/extratos
 *  - GET /v1/fornecimento-saldos-contas/saldos
 *
 * Não há paginação por página/cursor no contrato: o recorte do extrato é a
 * janela `dataInicio`/`dataFim`. Pra períodos longos use `extratoFatiado()`,
 * que quebra a janela e concatena os lançamentos.
 */
final class SaldoExtratoMethods extends BaseMethods implements StatementEndpoint
{
    private const PATH_EXTRATOS = '/v1/fornecimento-extratos-contas/extratos';

    private const PATH_SALDOS = '/v1/fornecimento-saldos-contas/saldos';

    /** Conta corrente. */
    public const TIPO_CONTA_CORRENTE = 'cc';

    /** Conta poupança. */
    public const TIPO_CONTA_POUPANCA = 'cp';

    /**
     * Consulta o extrato de uma conta PJ numa janela de datas.
     *
     * @param  int|string  $agencia  agência, até 5 dígitos
     * @param  int|string  $conta  número da conta, até 13 dígitos
     * @param  string  $tipo  'cc' (corrente) ou 'cp' (poupança)
     * @param  string|DateTimeInterface  $dataInicio  aceita DDMMAAAA, AAAA-MM-DD, DD/MM/AAAA ou objeto de data
     * @param  string|DateTimeInterface  $dataFim  idem
     * @param  string|null  $tipoOperacao  versão do programa no mainframe (opcional)
     */
    public function extratos(
        int|string $agencia,
        int|string $conta,
        string $tipo = self::TIPO_CONTA_CORRENTE,
        string|DateTimeInterface $dataInicio = '',
        string|DateTimeInterface $dataFim = '',
        ?string $tipoOperacao = null,
    ): ExtratoResponse {
        $query = [
            'agencia' => (string) $agencia,
            'conta' => (string) $conta,
            'tipo' => $tipo,
            'dataInicio' => self::formatarData($dataInicio),
            'dataFim' => self::formatarData($dataFim),
        ];

        if ($tipoOperacao !== null) {
            $query['tipoOperacao'] = $tipoOperacao;
        }

        $data = $this->makeRequest(HttpMethod::GET, self::PATH_EXTRATOS, query: $query);

        return ExtratoResponse::fromArray($data['data'] ?? $data);
    }

    /**
     * Consulta o saldo de uma conta PJ (composição por produto de saldo).
     *
     * @param  int|string  $agencia  agência, até 5 dígitos
     * @param  int|string  $conta  número da conta, até 13 dígitos
     * @param  string|null  $tipoOperacao  versão do programa no mainframe (opcional)
     */
    public function saldos(
        int|string $agencia,
        int|string $conta,
        ?string $tipoOperacao = null,
    ): SaldoResponse {
        $query = [
            'agencia' => (string) $agencia,
            'conta' => (string) $conta,
        ];

        if ($tipoOperacao !== null) {
            $query['tipoOperacao'] = $tipoOperacao;
        }

        $data = $this->makeRequest(HttpMethod::GET, self::PATH_SALDOS, query: $query);

        return SaldoResponse::fromArray($data['data'] ?? $data);
    }

    /**
     * Atalho de conciliação: só os lançamentos do extrato, achatados.
     *
     * @return array<int, Lancamento>
     */
    public function lancamentos(
        int|string $agencia,
        int|string $conta,
        string $tipo = self::TIPO_CONTA_CORRENTE,
        string|DateTimeInterface $dataInicio = '',
        string|DateTimeInterface $dataFim = '',
        ?string $tipoOperacao = null,
    ): array {
        return $this->extratos($agencia, $conta, $tipo, $dataInicio, $dataFim, $tipoOperacao)->lancamentos();
    }

    /**
     * "Paginação" do extrato: como o contrato não tem página nem cursor, o
     * único recorte é a janela de datas. Fatia o período em blocos de N dias e
     * concatena os lançamentos, na ordem cronológica das janelas.
     *
     * @param  int  $diasPorJanela  tamanho de cada fatia (o banco costuma limitar o período)
     * @return array<int, Lancamento>
     */
    public function extratoFatiado(
        int|string $agencia,
        int|string $conta,
        string|DateTimeInterface $dataInicio,
        string|DateTimeInterface $dataFim,
        string $tipo = self::TIPO_CONTA_CORRENTE,
        int $diasPorJanela = 30,
        ?string $tipoOperacao = null,
    ): array {
        $inicio = self::paraDate($dataInicio);
        $fim = self::paraDate($dataFim);

        if ($inicio === null || $fim === null || $diasPorJanela < 1) {
            return $this->lancamentos($agencia, $conta, $tipo, $dataInicio, $dataFim, $tipoOperacao);
        }

        $lancamentos = [];
        $cursor = $inicio;

        while ($cursor <= $fim) {
            $janelaFim = $cursor->modify('+'.($diasPorJanela - 1).' days');

            if ($janelaFim > $fim) {
                $janelaFim = $fim;
            }

            $lancamentos = array_merge(
                $lancamentos,
                $this->lancamentos($agencia, $conta, $tipo, $cursor, $janelaFim, $tipoOperacao),
            );

            $cursor = $janelaFim->modify('+1 day');
        }

        return $lancamentos;
    }

    /** Normaliza a data pro formato DDMMAAAA exigido pela API. */
    private static function formatarData(string|DateTimeInterface $data): string
    {
        if ($data instanceof DateTimeInterface) {
            return $data->format('dmY');
        }

        if ($data === '') {
            return '';
        }

        $digitos = preg_replace('/\D/', '', $data) ?? '';

        // Já veio DDMMAAAA.
        if (strlen($digitos) === 8 && preg_match('/^\d{8}$/', $data) === 1) {
            return $digitos;
        }

        $date = self::paraDate($data);

        return $date?->format('dmY') ?? $data;
    }

    /** Interpreta DDMMAAAA, AAAA-MM-DD, DD/MM/AAAA ou objeto de data. */
    private static function paraDate(string|DateTimeInterface $data): ?\DateTimeImmutable
    {
        if ($data instanceof DateTimeInterface) {
            return \DateTimeImmutable::createFromFormat('!Y-m-d', $data->format('Y-m-d')) ?: null;
        }

        if ($data === '') {
            return null;
        }

        $formato = match (true) {
            preg_match('/^\d{8}$/', $data) === 1 => '!dmY',
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) === 1 => '!Y-m-d',
            preg_match('#^\d{2}/\d{2}/\d{4}$#', $data) === 1 => '!d/m/Y',
            preg_match('/^\d{2}-\d{2}-\d{4}$/', $data) === 1 => '!d-m-Y',
            default => null,
        };

        if ($formato === null) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat($formato, $data);

        return $date === false ? null : $date;
    }

    /**
     * Contrato cross-bank StatementEndpoint: lançamentos do período, achatados
     * — é o formato que a conciliação bancária consome, igual ao do Itaú.
     *
     * A interface não recebe conta (é comum aos bancos), então agência/conta
     * saem das settings da integração (`agencia`, `conta` e, opcionalmente,
     * `tipo_conta`). Para escolher a conta explicitamente, use `lancamentos()`.
     *
     * @return array<int, Lancamento>
     */
    public function periodo(string $de, string $ate): array
    {
        $settings = $this->integration->getBankSettings();
        $agencia = $settings['agencia'] ?? null;
        $conta = $settings['conta'] ?? null;

        if ($agencia === null || $conta === null) {
            throw new RuntimeException(
                'Bradesco extrato: informe `agencia` e `conta` nas settings da integração '
                .'ou use lancamentos($agencia, $conta, ...) diretamente.'
            );
        }

        return $this->lancamentos(
            $agencia,
            $conta,
            (string) ($settings['tipo_conta'] ?? self::TIPO_CONTA_CORRENTE),
            $de,
            $ate,
        );
    }
}
