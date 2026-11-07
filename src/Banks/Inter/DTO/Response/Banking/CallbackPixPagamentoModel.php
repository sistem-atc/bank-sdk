<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CallbackPixPagamentoModel` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CallbackPixPagamentoModel`).
 *
 * @property array<int, ErroPagamento> $erros
 */
final class CallbackPixPagamentoModel implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoSolicitacao = null,
        public readonly ?string $chave = null,
        public readonly ?string $dataHoraMovimento = null,
        public readonly ?string $dataHoraSolicitacao = null,
        public readonly ?string $descricaoPagamento = null,
        public readonly ?string $endToEnd = null,
        public readonly ?string $instituicaoDestinatario = null,
        public readonly ?DadosConta $recebedor = null,
        #[ArrayOf(ErroPagamento::class)]
        public readonly array $erros = [],
        /** * `EFETIVADO` * `ERRO` * `REPROVADO` * `APROVACAO_EXPIRADA` * `CANCELADO` */
        public readonly ?string $status = null,
        /** * `PAGAMENTO` */
        public readonly ?string $tipoMovimentacao = null,
        public readonly ?string $valor = null,
    ) {}
}
