<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Posicao detalhada de fundos de investimento.
 *
 * Origem: GET /managers-position-mgmt/v1/detailedposition/funds/{cpfCnpj}/{accountCode}/{sourceCode}
 */
final class PosicaoDetalhadaFundosResponse implements DTOInterface
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
        /** Bloco de status da resposta. */
        public readonly ?RespostaBase $response = null,
        /** Codigo de retorno. */
        public readonly ?int $code = null,
        /** Descricao do retorno. */
        public readonly ?string $description = null,
        /** Detalhe das aplicacoes do fundo. @var array<int, PosicaoDetalhadaFundoItem> */
        #[ArrayOf(PosicaoDetalhadaFundoItem::class)]
        public readonly array $detailedFunds = [],
    ) {}
}
