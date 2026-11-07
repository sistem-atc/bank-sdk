<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CallbackPagamentoBoletoModel` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CallbackPagamentoBoletoModel`).
 *
 * @property array<int, CallbackPagamentoBoletoErrorModel> $erros
 */
final class CallbackPagamentoBoletoModel implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $linhaDigitavel = null,
        public readonly ?string $codigoTransacao = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $dataHoraSolicitacao = null,
        public readonly ?string $nomeBeneficiario = null,
        /** * `REALIZADO` * `AGENDADO` * `CANCELADO` * `ERRO` * `REPROVADO` * `APROVACAO_EXPIRADA` */
        public readonly ?string $status = null,
        /** O atributo em questão só é retornado em caso de Pagamento Boleto **REALIZADO**. */
        public readonly ?string $valorPago = null,
        /** O atributo em questão só é retornado em caso de de Pagamento Boleto **AGENDADO**. */
        public readonly ?string $valorAgendado = null,
        /** Número sequencial único, exclusivo para uso no Inter. */
        public readonly ?string $nsu = null,
        #[ArrayOf(CallbackPagamentoBoletoErrorModel::class)]
        public readonly array $erros = [],
    ) {}
}
