<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\BoletoNegociado;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Um boleto da API de Boletos Negociados (`api_boleto_v1`, base `/boleto/v1`) —
 * item de `GET /boletos` e retorno de `POST /boletos`.
 *
 * Campos de topo tipados; o bloco `pagador` fica como array cru (o schema
 * completo do pagador varia por tipo de pessoa/instrumento).
 */
final class BoletoNegociado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<string, mixed> $pagador */
    public function __construct(
        public readonly ?string $idBoleto = null,
        public readonly ?string $identificadorBoletoMercado = null,
        public readonly ?string $instrumentoCobranca = null,
        public readonly ?string $situacao = null,
        public readonly ?string $situacaoVencimento = null,
        public readonly ?bool $indicadorDescontado = null,
        public readonly ?string $seuNumero = null,
        public readonly ?string $nossoNumero = null,
        public readonly ?string $codigoCarteira = null,
        public readonly ?string $valor = null,
        public readonly ?string $dataEntrada = null,
        public readonly ?string $dataEmissao = null,
        public readonly ?string $dataVencimento = null,
        public readonly array $pagador = [],
    ) {}
}
