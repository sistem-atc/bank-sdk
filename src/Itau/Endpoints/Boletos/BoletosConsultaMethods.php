<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Boletos;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\BoletoConsultaResponse;

/**
 * Boletos Cobrança — CONSULTA de detalhe do boleto.
 * Produto do portal: "API Boletos - Consulta de detalhe do Boleto".
 * Base path: `/boletoscash/v2`.
 *
 * ATENÇÃO (wiring): em produção esta API vive em host próprio
 * `secure.api.cloud.itau.com.br`, DIFERENTE do host padrão de cash_management.
 * O connector precisa apontar o base_url pra esse host ao instanciar esta
 * classe (ou o path abaixo passa a valer sob outro host).
 */
final class BoletosConsultaMethods extends BaseMethods
{
    private const BASE = '/boletoscash/v2';

    /**
     * Consulta o detalhe de um boleto por beneficiário + carteira + nosso número.
     * `view`: 'basic' ou 'specific' (padrão 'specific' traz o detalhe completo).
     */
    public function consultarDetalhe(
        string $idBeneficiario,
        string $codigoCarteira,
        string $nossoNumero,
        string $view = 'specific',
    ): DTOInterface {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/boletos', query: [
            'id_beneficiario' => $idBeneficiario,
            'codigo_carteira' => $codigoCarteira,
            'nosso_numero' => $nossoNumero,
            'view' => $view,
        ]);

        return BoletoConsultaResponse::fromArray($data);
    }

    /**
     * Busca paginada de boletos (`boletos_search`) por filtros diversos
     * (id_beneficiario, codigo_carteira, nome_pagador, nosso_numero, view,
     * page, page_size, order_by, order).
     *
     * @param  array<string, mixed>  $filtros
     */
    public function buscar(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/boletos_search', query: $filtros);

        return BoletoConsultaResponse::fromArray($data);
    }
}
