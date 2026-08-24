<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Itau\Bases\BaseMethods;

/**
 * Resolvedores PÚBLICOS de payload de QR do arranjo Recebimentos Pix
 * (regulatório Bacen) — base `/regulatorio-pix/v2/qr`. A partir do
 * `pixUrlAccessToken` que vem embutido na URL do QR Code dinâmico, devolve o
 * payload da cobrança (imediata ou com vencimento) para o PSP pagador.
 *
 * São endpoints de leitura pública (não movimentam dinheiro); seguem o mesmo
 * host do produto (`pix_recebimentos`).
 */
final class QrCodeMethods extends BaseMethods
{
    private const BASE = '/regulatorio-pix/v2/qr';

    /**
     * Payload de uma cobrança IMEDIATA pelo token da URL —
     * GET /qr/{pixUrlAccessToken}.
     *
     * @return array<string, mixed>
     */
    public function imediata(string $pixUrlAccessToken): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($pixUrlAccessToken));
    }

    /**
     * Payload de uma cobrança COM VENCIMENTO pelo token da URL —
     * GET /qr/cobv/{pixUrlAccessToken}.
     *
     * @return array<string, mixed>
     */
    public function comVencimento(string $pixUrlAccessToken): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'/cobv/'.rawurlencode($pixUrlAccessToken));
    }
}
