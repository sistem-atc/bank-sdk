<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CobVCompleta;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CobVGerada;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CobsVConsultadas;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\PagarCobrancaPixResponse;

/**
 * Cobrança Pix COM VENCIMENTO (COBV) — `/pix/v2/cobv`. Escopos `cobv.read` /
 * `cobv.write`. Aceita multa, juros, desconto e abatimento; o txid é sempre
 * do recebedor.
 */
final class CobVMethods extends BaseMethods
{
    use Concerns;

    /** `PUT /cobv/{txid}`. @param array<string, mixed> $dados corpo CobVSolicitada */
    public function criar(string $txid, array $dados): CobVGerada
    {
        return CobVGerada::fromArray($this->makeRequest(HttpMethod::PUT, '/cobv/'.rawurlencode($txid), body: $dados));
    }

    /** `PATCH /cobv/{txid}`. @param array<string, mixed> $dados corpo CobVRevisada */
    public function revisar(string $txid, array $dados): CobVGerada
    {
        return CobVGerada::fromArray($this->makeRequest(HttpMethod::PATCH, '/cobv/'.rawurlencode($txid), body: $dados));
    }

    public function consultar(string $txid): CobVCompleta
    {
        return CobVCompleta::fromArray($this->makeRequest(HttpMethod::GET, '/cobv/'.rawurlencode($txid)));
    }

    /**
     * `GET /cobv` — período em RFC 3339.
     *
     * @param  array{cpf?: string, cnpj?: string, locationPresente?: bool, status?: string, loteCobVId?: int, paginaAtual?: int, itensPorPagina?: int}  $filtros
     */
    public function listar(string $inicio, string $fim, array $filtros = []): CobsVConsultadas
    {
        return CobsVConsultadas::fromArray($this->makeRequest(HttpMethod::GET, '/cobv', query: self::periodo($inicio, $fim, $filtros)));
    }

    /** `POST /cobv/pagar/{txid}` — paga uma COBV com saldo da conta. MOVIMENTA dinheiro. @param array<string, mixed> $dados */
    public function pagar(string $txid, array $dados = []): PagarCobrancaPixResponse
    {
        return PagarCobrancaPixResponse::fromArray($this->makeRequest(HttpMethod::POST, '/cobv/pagar/'.rawurlencode($txid), body: $dados));
    }
}
