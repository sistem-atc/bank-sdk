<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Efeito da solicitação de baixa: quando foi solicitada e como o status do
 * título mudou.
 * Origem: POST /boleto/cobranca-baixa/v1/baixar (campo `dados`)
 */
final class BaixaTituloDados implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data/hora da solicitação de baixa. */
        public readonly ?string $dataHoraSolicitacao = null,
        /** Status do título após a solicitação. */
        public readonly ?int $status = null,
        /** Status do título antes da solicitação. */
        public readonly ?int $statusAnterior = null,
    ) {}
}
