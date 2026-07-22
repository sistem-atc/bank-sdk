<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Contrato de recorrência do Pix Automático — resposta de
 * `POST /rec`, `GET /rec/{idRec}` e `PATCH /rec/{idRec}`. Também é o
 * `recPayload` embutido na solicitação de confirmação.
 */
final class Recorrencia implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Atualizacao> $atualizacao */
    public function __construct(
        public readonly ?string $idRec = null,
        public readonly ?string $status = null,
        public readonly ?string $politicaRetentativa = null,
        public readonly ?Valor $valor = null,
        public readonly ?Vinculo $vinculo = null,
        public readonly ?Calendario $calendario = null,
        public readonly ?Pagador $pagador = null,
        public readonly ?Recebedor $recebedor = null,
        public readonly ?Loc $loc = null,
        public readonly ?DadosQR $dadosQR = null,
        public readonly ?Ativacao $ativacao = null,
        #[ArrayOf(Atualizacao::class)]
        public readonly array $atualizacao = [],
    ) {}
}
