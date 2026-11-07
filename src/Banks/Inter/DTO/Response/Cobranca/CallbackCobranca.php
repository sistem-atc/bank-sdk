<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CallbackCobranca` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CallbackCobranca`).
 */
final class CallbackCobranca implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identificador único da cobrança */
        public readonly ?string $codigoSolicitacao = null,
        /** Seu Número, enviado na requisição para inclusão do título. */
        public readonly ?string $seuNumero = null,
        /** Possíveis situações de uma cobranca. */
        public readonly ?string $situacao = null,
        public readonly ?string $dataHoraSituacao = null,
        public readonly ?string $valorTotalRecebido = null,
        public readonly ?string $origemRecebimento = null,
        /** Nosso Número, atribuído automaticamente ao longo da inclusão do título. */
        public readonly ?string $nossoNumero = null,
        /** Dígitos que compõem o código de barras do boleto (44 posições preenchidas) */
        public readonly ?string $codigoBarras = null,
        /** Dígitos que compõem a linha digitável do boleto, sem formatação (47 posições preenchidas) */
        public readonly ?string $linhaDigitavel = null,
        /** Txid da Cobrança PIX gerada */
        public readonly ?string $txid = null,
        /** QRCode de cobrança para pagamento */
        public readonly ?string $pixCopiaECola = null,
    ) {}
}
