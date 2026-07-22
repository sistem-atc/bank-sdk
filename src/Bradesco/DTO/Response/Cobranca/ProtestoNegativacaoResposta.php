<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno de protesto, sustação de protesto, negativação ou cancelamento de
 * negativação de um boleto.
 * Origem: POST /boleto/cobranca-protesto-negativacao/v1/executar
 */
final class ProtestoNegativacaoResposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código de status da operação. */
        public readonly ?int $status = null,
        /** Identificador único da transação. */
        public readonly ?string $transacao = null,
        /** Mensagem descritiva do resultado. */
        public readonly ?string $mensagem = null,
        /** Causa detalhada do resultado. */
        public readonly ?string $causa = null,
        /** Data/hora da solicitação. */
        public readonly ?string $dataHoraSolicitacao = null,
        /** Situação do título após o comando. */
        public readonly ?int $situacaoAtual = null,
        /** Situação do título antes do comando. */
        public readonly ?int $situacaoAnterior = null,
    ) {}
}
