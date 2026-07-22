<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança recorrente (CobR) — agendamento de uma cobrança sob um contrato de
 * recorrência. Resposta de `POST /cobr`, `PUT|PATCH|GET /cobr/{txid}` e
 * `POST /cobr/{txid}/retentativa/{data}`.
 */
final class CobrancaRecorrente implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param array<int, Tentativa>   $tentativas
     * @param array<int, Atualizacao> $atualizacao
     * @param array<int, Pix>         $pix
     */
    public function __construct(
        public readonly ?string $idRec = null,
        public readonly ?string $txid = null,
        public readonly ?string $infoAdicional = null,
        public readonly ?string $status = null,
        public readonly ?string $politicaRetentativa = null,
        public readonly ?bool $ajusteDiaUtil = null,
        public readonly ?Calendario $calendario = null,
        public readonly ?Valor $valor = null,
        public readonly ?Devedor $devedor = null,
        public readonly ?Recebedor $recebedor = null,
        public readonly ?Encerramento $encerramento = null,
        #[ArrayOf(Tentativa::class)]
        public readonly array $tentativas = [],
        #[ArrayOf(Atualizacao::class)]
        public readonly array $atualizacao = [],
        #[ArrayOf(Pix::class)]
        public readonly array $pix = [],
    ) {}
}
