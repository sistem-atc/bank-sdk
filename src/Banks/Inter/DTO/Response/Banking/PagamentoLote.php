<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PagamentoLote` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PagamentoLote`).
 */
final class PagamentoLote implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $tipoPagamento = null,
        public readonly ?string $detalhe = null,
        /** Identificador único do pagamento ao ser registrado. */
        public readonly ?string $codigoTransacao = null,
        /** * EMPROCESSAMENTO não representa um estado do serviço de pagamentos. É o estado inicial do pagamento */
        public readonly ?string $status = null,
        /** Código de barras ou linha digitável */
        public readonly ?string $codBarraLinhaDigitavel = null,
        /** Valor a ser pago. */
        public readonly ?float $valorPagar = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $dataVencimento = null,
        /** Número sequencial único, exclusivo para uso no Inter. */
        public readonly ?string $nsu = null,
        /** Identificador único do pagamento ao ser registrado. */
        public readonly ?string $codigoSolicitacao = null,
        public readonly ?string $tipoDarf = null,
        public readonly ?string $cnpjCpf = null,
        public readonly ?string $descricao = null,
        public readonly ?string $nomeEmpresa = null,
        public readonly ?string $telefoneEmpresa = null,
        public readonly ?float $valor = null,
        public readonly ?float $valorMulta = null,
        public readonly ?float $valorJuros = null,
        public readonly ?float $valorTotal = null,
        public readonly ?string $periodoApuracao = null,
        public readonly ?string $referencia = null,
        public readonly ?string $codigoReceita = null,
    ) {}
}
