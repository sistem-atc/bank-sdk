<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PixAsyncResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PixAsyncResponse`).
 *
 * @property array<int, ErroPagamento> $erros
 */
final class PixAsyncResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $contaCorrente = null,
        public readonly ?DadosConta $recebedor = null,
        #[ArrayOf(ErroPagamento::class)]
        public readonly array $erros = [],
        public readonly ?string $endToEnd = null,
        public readonly ?float $valor = null,
        /** Status atual da transação Pix * `CRIADO` * `AGUARDANDO_APROVACAO` * `APROVADO` * `REPROVADO` * `EXPIRADO` * `CANCELADO` * `FALHA` * `AGENDADO` * `PAGO… */
        public readonly ?string $status = null,
        public readonly ?string $dataHoraMovimento = null,
        public readonly ?string $dataHoraSolicitacao = null,
        public readonly ?string $chave = null,
        public readonly ?string $codigoSolicitacao = null,
    ) {}
}
