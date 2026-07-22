<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `dados_individuais_boleto` — o título em si (nosso número, valor,
 * vencimento, código de barras e linha digitável). Presente na emissão
 * (cash_management/v2) e na consulta de detalhe (boletoscash/v2). Valores
 * monetários vêm como string; `dac_titulo`/`numero_nosso_numero` ora string
 * ora inteiro, mantidos como string.
 */
final class DadoIndividualBoleto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idBoletoIndividual = null,
        public readonly ?string $numeroNossoNumero = null,
        public readonly ?string $dacTitulo = null,
        public readonly ?string $dataVencimento = null,
        public readonly ?string $valorTitulo = null,
        public readonly ?string $codigoBarras = null,
        public readonly ?string $numeroLinhaDigitavel = null,
        public readonly ?string $dataLimitePagamento = null,
        public readonly ?string $textoSeuNumero = null,
        public readonly ?string $textoUsoBeneficiario = null,
        public readonly ?string $situacaoGeralBoleto = null,
        public readonly ?string $statusVencimento = null,
    ) {}
}
