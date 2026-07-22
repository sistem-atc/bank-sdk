<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `calendario` do Pix Automático. Reúne os campos das três entidades:
 * recorrência (`dataInicial`/`dataFinal`/`periodicidade`), cobrança recorrente
 * (`criacao`/`dataDeVencimento`) e solicitação (`dataExpiracaoSolicitacao`).
 */
final class Calendario implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dataInicial = null,
        public readonly ?string $dataFinal = null,
        public readonly ?string $periodicidade = null,
        public readonly ?string $criacao = null,
        public readonly ?string $dataDeVencimento = null,
        public readonly ?string $dataExpiracaoSolicitacao = null,
    ) {}
}
