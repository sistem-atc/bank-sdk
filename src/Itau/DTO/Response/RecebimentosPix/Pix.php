<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Pix recebido (QR Code ou transferência) — recurso de `GET /pix/{e2eid}`, item
 * de `GET /pix` e payload da notificação via webhook. `valor` é string decimal;
 * `pagador` só vem no Webhook Exclusivo.
 *
 * @property array<int, Devolucao> $devolucoes
 */
final class Pix implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Devolucao> $devolucoes */
    public function __construct(
        public readonly ?string $endToEndId = null,
        public readonly ?string $txid = null,
        public readonly ?string $valor = null,
        public readonly ?string $horario = null,
        public readonly ?string $infoPagador = null,
        public readonly ?string $chave = null,
        public readonly ?ComponentesValor $componentesValor = null,
        public readonly ?Pagador $pagador = null,
        #[ArrayOf(Devolucao::class)]
        public readonly array $devolucoes = [],
    ) {}
}
