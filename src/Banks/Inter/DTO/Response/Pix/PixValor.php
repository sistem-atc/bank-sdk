<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * O objetivo dessa estrutura é explicar os elementos de composição do valor do
 * Pix, incluindo informações sobre as multas, juros, descontos e abatimentos
 * quando o Pix for relativo a cobranças com vencimento. Regras da estrutura: -
 * O valor do Pix é igual a: - (original.valor + saque.valor + troco.valor) +
 * multa.valor + juros.valor – abatimento.valor – desconto.valor considerando-
 * se apenas os campos que estiverem presentes para cada tipo de cobrança pago.
 * - As estruturas saque e troco só serão retornadas quando o Pix for relativo
 * a um Pix Saque ou Pix Troco, respectivamente, e as demais estruturas
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PixValor`).
 */
final class PixValor implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?PixValorOriginal $original = null,
        public readonly ?PixValorSaque $saque = null,
        public readonly ?PixValorSaque $troco = null,
        public readonly ?PixValorOriginal $juros = null,
        public readonly ?PixValorOriginal $multa = null,
        public readonly ?PixValorOriginal $abatimento = null,
        /** Filtro pela existência de txid. */
        public readonly ?bool $txIdPresente = null,
        /** Filtro pela existência de devolução. */
        public readonly ?bool $devolucaoPresente = null,
        /** CPF */
        public readonly ?string $cpf = null,
        /** CNPJ */
        public readonly ?string $cnpj = null,
        public readonly ?Paginacao $paginacao = null,
        public readonly ?PixValorOriginal $desconto = null,
    ) {}
}
