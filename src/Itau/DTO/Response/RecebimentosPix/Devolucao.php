<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Devolução de um Pix recebido — resposta de `PUT|GET /pix/{e2eid}/devolucao/{id}`
 * e item do array `pix.devolucoes`. O processo no Itaú é síncrono, logo o `PUT`
 * já retorna o status final.
 *
 * `natureza` ∈ {ORIGINAL, RETIRADA, MED_OPERACIONAL, MED_FRAUDE, MED_PIX_AUTOMATICO};
 * `status` ∈ {EM_PROCESSAMENTO, DEVOLVIDO, NAO_REALIZADO}. Valor em string decimal.
 */
final class Devolucao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $rtrId = null,
        public readonly ?string $valor = null,
        public readonly ?string $natureza = null,
        public readonly ?string $descricao = null,
        public readonly ?string $status = null,
        public readonly ?string $motivo = null,
        public readonly ?HorarioDevolucao $horario = null,
    ) {}
}
