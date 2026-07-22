<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Solicitação de confirmação (aceite) de recorrência — resposta de
 * `POST /solicrec`, `GET /solicrec/{idSolicRec}` e
 * `PATCH /solicrec/{idSolicRec}`.
 */
final class SolicitacaoRecorrencia implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Atualizacao> $atualizacao */
    public function __construct(
        public readonly ?string $idSolicRec = null,
        public readonly ?string $idRec = null,
        public readonly ?string $status = null,
        public readonly ?Calendario $calendario = null,
        public readonly ?Destinatario $destinatario = null,
        public readonly ?Recorrencia $recPayload = null,
        #[ArrayOf(Atualizacao::class)]
        public readonly array $atualizacao = [],
    ) {}
}
