<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\PayloadLocation;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\PayloadLocationCompleta;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\PayloadLocationConsultadas;

/**
 * Locations de payload (QR Code dinâmico reutilizável) — `/pix/v2/loc`.
 * Escopos `payloadlocation.read` / `.write`.
 */
final class LocationMethods extends BaseMethods
{
    use Concerns;

    /** `POST /loc`. @param 'cob'|'cobv' $tipoCob */
    public function criar(string $tipoCob = 'cob'): PayloadLocation
    {
        return PayloadLocation::fromArray($this->makeRequest(HttpMethod::POST, '/loc', body: ['tipoCob' => $tipoCob]));
    }

    public function consultar(int|string $id): PayloadLocationCompleta
    {
        return PayloadLocationCompleta::fromArray($this->makeRequest(HttpMethod::GET, '/loc/'.rawurlencode((string) $id)));
    }

    /** @param array{txIdPresente?: bool, tipoCob?: 'cob'|'cobv', paginaAtual?: int, itensPorPagina?: int} $filtros */
    public function listar(string $inicio, string $fim, array $filtros = []): PayloadLocationConsultadas
    {
        return PayloadLocationConsultadas::fromArray($this->makeRequest(HttpMethod::GET, '/loc', query: self::periodo($inicio, $fim, $filtros)));
    }

    /** `DELETE /loc/{id}/txid` — desvincula a cobrança da location. */
    public function desvincular(int|string $id): PayloadLocation
    {
        return PayloadLocation::fromArray($this->makeRequest(HttpMethod::DELETE, '/loc/'.rawurlencode((string) $id).'/txid'));
    }
}
