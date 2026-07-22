<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco de baixa embutido no título consultado (código, descrição e data).
 * Origem: POST /boleto/cobranca-consulta/v1/consultar (campo `titulo.baixa`)
 */
final class BaixaTitulo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código da baixa. */
        public readonly ?int $codigo = null,
        /** Descrição da baixa. */
        public readonly ?string $descricao = null,
        /** Data da baixa (AAAAMMDD). */
        public readonly ?int $data = null,
    ) {}
}
