<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\PixAutomatico;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\Cobranca;

/**
 * API "Emissão de QR Code Pix Automático" — geração/consulta do QR Code Pix
 * atrelado à recorrência. Grupo `/cobrancas`.
 *
 * Host de produção dedicado (COM prefixo de versão):
 * `https://recebimentos-pix.api.itau.com/qrcode-pix-automatico/v1`.
 *
 * NOTA: a spec publica apenas os PATHS (`/cobrancas`, `/cobrancas/{cobrancaId}`)
 * — não traz o schema de request/response. Os métodos abaixo seguem o padrão
 * criar/consultar; o DTO Cobranca reflete os campos usuais do arranjo Pix e
 * deve ser revisado quando o contrato detalhado for liberado.
 */
final class QrCodeMethods extends BaseMethods
{
    private const BASE = '/qrcode-pix-automatico/v1/cobrancas';

    /**
     * Emitir (criar) QR Code de cobrança Pix Automático — POST /cobrancas.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::BASE, body: $dados);

        return Cobranca::fromArray($data);
    }

    /**
     * Consultar QR Code de cobrança — GET /cobrancas/{cobrancaId}.
     */
    public function consultar(string $cobrancaId): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($cobrancaId));

        return Cobranca::fromArray($data);
    }
}
