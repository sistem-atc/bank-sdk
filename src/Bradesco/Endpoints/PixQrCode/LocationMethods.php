<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixQrCode;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaLocations;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Location;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Locations do payload Pix (`loc`) — a URL que o QR Code aponta. Um `loc` é
 * criado por tipo de cobrança (`cob` ou `cobv`) e depois vinculado a um txid.
 *
 * Cobre `/v2/loc` (criar, listar), `/v2/loc/{id}` (consultar) e
 * `/v2/loc/{id}/txid` (desvincular a cobrança do location).
 *
 * FAMÍLIA PIX — host `qrpix.bradesco.com.br` e autorizador `/v2/oauth/token`.
 */
final class LocationMethods extends BaseMethods
{
    private const PATH = '/v2/loc';

    protected function family(): string
    {
        return BradescoHosts::FAMILY_PIX;
    }

    /**
     * Cria um location de payload — POST /v2/loc.
     *
     * @param  array{tipoCob?: string}  $dados  tipoCob: 'cob' ou 'cobv'
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH, body: $dados);

        return Location::fromArray($data);
    }

    /**
     * Lista locations do período — GET /v2/loc.
     *
     * @param  array<string, mixed>  $filtros  inicio e fim OBRIGATÓRIOS; opcionais:
     *                                         txIdPresente, tipoCob,
     *                                         'paginacao.paginaAtual', 'paginacao.itensPorPagina'
     */
    public function listar(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH, query: $filtros);

        return ListaLocations::fromArray($data);
    }

    /** Recupera um location pelo id — GET /v2/loc/{id}. */
    public function consultar(int|string $id): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH.'/'.rawurlencode((string) $id));

        return Location::fromArray($data);
    }

    /**
     * Desvincula a cobrança (txid) do location — DELETE /v2/loc/{id}/txid.
     * O location continua existindo e pode ser reaproveitado.
     */
    public function desvincularTxid(int|string $id): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::DELETE,
            self::PATH.'/'.rawurlencode((string) $id).'/txid',
        );

        return Location::fromArray($data);
    }
}
