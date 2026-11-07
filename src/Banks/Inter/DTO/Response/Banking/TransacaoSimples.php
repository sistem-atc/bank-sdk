<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `TransacaoSimples` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `TransacaoSimples`).
 */
final class TransacaoSimples implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cpmf = null,
        public readonly ?string $dataEntrada = null,
        /** Transação vinculada a movimentação * `DEBITO_EM_CONTA` * `DEPOSITO_BOLETO` * `ANTECIPACAO_RECEBIVEIS` * `ANTECIPACAO_RECEBIVEIS_CARTAO` * `BOLETO_COBR… */
        public readonly ?string $tipoTransacao = null,
        /** Tipo de operação realizada * `D` - Débito(Saída) * `C` - Crédito(Entrada) */
        public readonly ?string $tipoOperacao = null,
        public readonly ?string $valor = null,
        public readonly ?string $titulo = null,
        public readonly ?string $descricao = null,
    ) {}
}
