<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da alteração (comando de instrução) de um título já registrado.
 * Origem: PUT /boleto/cobranca-altera/v1/alterar
 */
final class AlteracaoBoletoResposta implements DTOInterface
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
    ) {}
}
