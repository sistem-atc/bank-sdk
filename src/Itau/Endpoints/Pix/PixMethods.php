<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Pix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Sispag\PagamentoDetalhe;
use SistemAtc\Banks\Itau\DTO\Response\Sispag\TransferenciaResponse;

/**
 * PIX de SAÍDA via SISPAG (Cash Management) — `POST /sispag/v1/transferencias`.
 * Cobre Pix por dados da conta, por chave e QR Code (o body carrega a
 * identificação — conta_recebedor / chave / pix_link|emv|url).
 *
 * MOVIMENTA dinheiro. A consulta reaproveita o detalhe unificado do SISPAG.
 */
final class PixMethods extends BaseMethods implements PixEndpoint
{
    private const PATH_INCLUIR = '/sispag/v1/transferencias';

    private const PATH_DETALHE = '/sispag/v1/pagamentos_sispag';

    /**
     * @param  array{valor_pagamento?: string, data_pagamento?: string, chave?: string, conta_recebedor?: string, pagador?: array<string, mixed>, identificador?: string}  $dados
     */
    public function pagar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_INCLUIR, body: $dados);

        return TransferenciaResponse::fromArray($data);
    }

    public function consultar(string $identificador): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::PATH_DETALHE.'/'.rawurlencode($identificador),
        );

        // O detalhe vem embrulhado em `data` (mesmo padrão da listagem).
        return PagamentoDetalhe::fromArray($data['data'] ?? $data);
    }
}
