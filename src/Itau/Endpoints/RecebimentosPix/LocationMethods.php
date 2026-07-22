<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Location;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\LocationList;

/**
 * Locations (`/regulatorio-pix/v2/loc`) — códigos reaproveitáveis para vincular
 * diferentes QR Codes a uma mesma imagem. `tipoCob` define se a location serve
 * cobrança imediata (`cob`) ou com vencimento (`cobv`).
 */
final class LocationMethods extends BaseMethods
{
    private const BASE = '/regulatorio-pix/v2/loc';

    /**
     * Cria uma location (POST /loc). Body: `{"tipoCob": "cob"|"cobv"}`.
     *
     * @param array<string, mixed> $dados
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::BASE, body: $dados);

        return Location::fromArray($data);
    }

    /** Consulta uma location específica pelo id (GET /loc/{id}). */
    public function consultar(int|string $id): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode((string) $id));

        return Location::fromArray($data);
    }

    /**
     * Lista locations por período/filtros (GET /loc).
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return LocationList::fromArray($data);
    }

    /** Desvincula o QR Code (txid) associado a uma location (DELETE /loc/{id}/txid). */
    public function desvincularTxid(int|string $id): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::DELETE, self::BASE.'/'.rawurlencode((string) $id).'/txid');

        return Location::fromArray($data);
    }
}
