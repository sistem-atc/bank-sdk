<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Cobranca;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\CobrancaList;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\QrCode;

/**
 * QR Code com vencimento (COBV) da API regulatória Pix do Bacen — base
 * `/regulatorio-pix/v2/cobv`. Equivalente a um boleto: data de vencimento, juros,
 * multa, desconto e abatimento no objeto `valor`.
 */
final class CobrancaVencimentoMethods extends BaseMethods
{
    private const BASE = '/regulatorio-pix/v2/cobv';

    /**
     * Emite uma cobrança com vencimento com txid informado (PUT /cobv/{txid}).
     *
     * @param array<string, mixed> $dados
     */
    public function criar(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PUT, self::BASE.'/'.rawurlencode($txid), body: $dados);

        return Cobranca::fromArray($data);
    }

    /**
     * Altera ou cancela uma cobrança com vencimento (PATCH /cobv/{txid}).
     *
     * @param array<string, mixed> $dados
     */
    public function revisar(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PATCH, self::BASE.'/'.rawurlencode($txid), body: $dados);

        return Cobranca::fromArray($data);
    }

    /** Consulta uma cobrança com vencimento específica (GET /cobv/{txid}). */
    public function consultar(string $txid): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($txid));

        return Cobranca::fromArray($data);
    }

    /**
     * Lista cobranças com vencimento por período/filtros (GET /cobv).
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return CobrancaList::fromArray($data);
    }

    /**
     * Obtém a imagem/payload do QR Code de uma cobrança com vencimento
     * (GET /cobv/{txid}/qrcode — endpoint em obsolescência).
     */
    public function obterQrCode(string $txid): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($txid).'/qrcode');

        return QrCode::fromArray($data);
    }
}
