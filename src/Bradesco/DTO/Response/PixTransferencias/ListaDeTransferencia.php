<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista paginada de transferências.
 *
 * Schema `ListaDeTransferencia` da spec "Pix Transferência - Consultar".
 * Os endpoints implementados aqui (consulta por id-transacao e por
 * id-transacao + e2e) devolvem `TransferenciaResponse` unitário — este DTO
 * cobre o schema de listagem declarado na spec.
 *
 * @property TransferenciaResponse[] $transferencias
 */
final class ListaDeTransferencia implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param TransferenciaResponse[] $transferencias */
    public function __construct(
        /** Parâmetros utilizados na consulta. */
        public readonly ?ParametrosResponse $parametros = null,
        /** Lista de transferências. */
        #[ArrayOf(TransferenciaResponse::class)]
        public readonly array $transferencias = [],
    ) {}
}
