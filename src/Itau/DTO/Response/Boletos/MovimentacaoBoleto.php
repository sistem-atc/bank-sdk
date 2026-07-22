<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `data[]` do extrato detalhado de movimentações — `GET /extrato/v1/
 * francesas/{francesaId}/movimentacoes` (produto "Extrato Boleto Cobrança").
 * `codigo_status` e `tipo_movimentacao` seguem as tabelas de status do extrato.
 * Valores monetários vêm como string decimal.
 */
final class MovimentacaoBoleto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $agencia = null,
        public readonly ?int $conta = null,
        public readonly ?string $dataMovimentacao = null,
        public readonly ?int $numeroCarteira = null,
        public readonly ?string $codigoStatus = null,
        public readonly ?string $tipoMovimentacao = null,
        public readonly ?int $nossoNumero = null,
        public readonly ?string $seuNumero = null,
        public readonly ?string $dacTitulo = null,
        public readonly ?string $tipoCobranca = null,
        public readonly ?int $sequenciaTitulo = null,
        public readonly ?string $pagador = null,
        public readonly ?int $agenciaRecebedora = null,
        public readonly ?string $dataMovimentacaoTituloCarteira = null,
        public readonly ?string $dataInclusao = null,
        public readonly ?string $dataVencimento = null,
        public readonly ?string $valorTitulo = null,
        public readonly ?string $valorLiquidoLancado = null,
        public readonly ?string $valorAcrescimo = null,
        public readonly ?string $valorDecrescimo = null,
        public readonly ?string $indicadorPagamentoReservaAdministrativa = null,
        public readonly ?bool $indicadorRateioCredito = null,
        public readonly ?int $dacAgenciaContaBeneficiario = null,
    ) {}
}
