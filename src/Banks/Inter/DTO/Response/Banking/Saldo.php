<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Saldo` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Saldo`).
 */
final class Saldo implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Saldo bloqueado (cheque). Retornando apenas quando **não** é informado a "dataSaldo" na requisição. */
        public readonly ?float $bloqueadoCheque = null,
        /** Saldo líquido */
        public readonly ?float $disponivel = null,
        /** Saldo bloqueado (justiça). Retornando apenas quando **não** é informado a "dataSaldo" na requisição. */
        public readonly ?float $bloqueadoJudicialmente = null,
        /** Saldo bloqueado (justiça). Retornando apenas quando **não** é informado a "dataSaldo" na requisição. */
        public readonly ?float $bloqueadoAdministrativo = null,
        /** Limite de credito da conta. Retornando apenas quando **não** é informado a "dataSaldo" na requisição. */
        public readonly ?float $limite = null,
        /** A data referência será retornada quando a consulta for realizada em um dia não útil. Visto que o saldo não é sensibilizado em dias não úteis, será ret… */
        public readonly ?string $dataReferencia = null,
    ) {}
}
