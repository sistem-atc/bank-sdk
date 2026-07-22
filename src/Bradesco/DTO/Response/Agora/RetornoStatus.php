<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Par status/mensagem devolvido pelo servico de liquidacao.
 *
 * Origem: components.schemas.Regress.
 */
final class RetornoStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Modelo de liquidacao: 0 = Agora, 1 = Bradesco. */
        public readonly ?int $status = null,
        /** Descricao do status. */
        public readonly ?string $message = null,
    ) {}
}
