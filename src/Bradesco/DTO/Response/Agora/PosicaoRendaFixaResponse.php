<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Posicao detalhada de renda fixa.
 *
 * ATENCAO: neste endpoint o campo `response` NAO e o bloco de status das
 * demais posicoes — e a LISTA de titulos de renda fixa (assim esta na spec).
 *
 * Origem: GET /managers-position-mgmt/v1/detailedposition/fixedIncome/{cpfCnpj}/{accountCode}
 */
final class PosicaoRendaFixaResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Metadados da consulta. */
        public readonly ?Meta $meta = null,
        /** Status code da resposta. */
        public readonly ?int $statusCode = null,
        /** Erros retornados pelo backend. @var array<int, ErroApi> */
        #[ArrayOf(ErroApi::class)]
        public readonly array $errors = [],
        /** Titulos de renda fixa em custodia. @var array<int, RendaFixaItem> */
        #[ArrayOf(RendaFixaItem::class)]
        public readonly array $response = [],
        /** Codigo de retorno. */
        public readonly ?int $code = null,
        /** Descricao do retorno. */
        public readonly ?string $description = null,
        /** Valor bruto total da carteira de renda fixa. */
        public readonly ?float $totalGrossValue = null,
    ) {}
}
