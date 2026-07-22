<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da solicitação de baixa de título.
 * Origem: POST /boleto/cobranca-baixa/v1/baixar
 */
final class BaixaTituloResposta implements DTOInterface
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
        /** Detalhe da baixa efetuada. */
        public readonly ?BaixaTituloDados $dados = null,
    ) {}
}
