<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixQrCode;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaEstatica;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança estática Pix (`cobe`) — QR Code SEM expiração e sem location,
 * gerado direto da chave. Único endpoint do recurso: POST `/v1/cobe`.
 *
 * Devolve o copia-e-cola (`pixCopiaECola`) e o PNG do QR em `base64`.
 *
 * FAMÍLIA PIX — host `qrpix.bradesco.com.br` e autorizador `/v2/oauth/token`.
 */
final class CobrancaEstaticaMethods extends BaseMethods
{
    private const PATH = '/v1/cobe';

    protected function family(): string
    {
        return BradescoHosts::FAMILY_PIX;
    }

    /**
     * Gera a cobrança estática — POST /v1/cobe.
     *
     * @param  array{txid?: string, valor?: string, chave?: string, solicitacaoPagador?: string, nomePersonalizacaoQr?: string}  $dados
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH, body: $dados);

        return CobrancaEstatica::fromArray($data);
    }
}
