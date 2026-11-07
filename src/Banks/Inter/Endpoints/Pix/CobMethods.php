<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CobCompleta;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CobGerada;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CobsConsultadas;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\PagarCobrancaPixResponse;

/**
 * Cobrança Pix IMEDIATA (COB) — `/pix/v2/cob`, padrão Bacen. Escopos
 * `cob.read` / `cob.write`. Gerencia o QR Code de RECEBIMENTO; não paga nada.
 */
final class CobMethods extends BaseMethods
{
    use Concerns;

    /** `POST /cob` — o Inter gera o txid. @param array<string, mixed> $dados corpo CobSolicitada */
    public function criar(array $dados): CobGerada
    {
        return CobGerada::fromArray($this->makeRequest(HttpMethod::POST, '/cob', body: $dados));
    }

    /** `PUT /cob/{txid}` — txid escolhido pelo recebedor (26 a 35 caracteres). @param array<string, mixed> $dados */
    public function criarComTxid(string $txid, array $dados): CobGerada
    {
        return CobGerada::fromArray($this->makeRequest(HttpMethod::PUT, '/cob/'.rawurlencode($txid), body: $dados));
    }

    /**
     * `PATCH /cob/{txid}` — revisa ou cancela. Pra cancelar, mande
     * `['status' => 'REMOVIDA_PELO_USUARIO_RECEBEDOR']`.
     *
     * @param  array<string, mixed>  $dados  corpo CobRevisada
     */
    public function revisar(string $txid, array $dados): CobGerada
    {
        return CobGerada::fromArray($this->makeRequest(HttpMethod::PATCH, '/cob/'.rawurlencode($txid), body: $dados));
    }

    public function consultar(string $txid): CobCompleta
    {
        return CobCompleta::fromArray($this->makeRequest(HttpMethod::GET, '/cob/'.rawurlencode($txid)));
    }

    /**
     * `GET /cob` — período em RFC 3339 (`2026-11-01T00:00:00Z`).
     *
     * @param  array{cpf?: string, cnpj?: string, locationPresente?: bool, status?: string, paginaAtual?: int, itensPorPagina?: int}  $filtros
     */
    public function listar(string $inicio, string $fim, array $filtros = []): CobsConsultadas
    {
        return CobsConsultadas::fromArray($this->makeRequest(HttpMethod::GET, '/cob', query: self::periodo($inicio, $fim, $filtros)));
    }

    /**
     * `POST /cob/pagar/{txid}` — paga uma COB com saldo da conta (escopo
     * `pix.write`). MOVIMENTA dinheiro.
     *
     * @param  array<string, mixed>  $dados  corpo PagarCobrancaPix
     */
    public function pagar(string $txid, array $dados = []): PagarCobrancaPixResponse
    {
        return PagarCobrancaPixResponse::fromArray($this->makeRequest(HttpMethod::POST, '/cob/pagar/'.rawurlencode($txid), body: $dados));
    }
}
