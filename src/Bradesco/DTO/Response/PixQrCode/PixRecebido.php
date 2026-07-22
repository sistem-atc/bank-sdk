<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Pix recebido (liquidado) — `/v2/pix` e o array `pix` dentro das cobranças.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class PixRecebido implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $endToEndId = null,
        public readonly ?string $txid = null,
        public readonly ?string $valor = null,
        public readonly ?string $horario = null,
        public readonly ?string $infoPagador = null,
        public readonly ?ComponentesValor $componentesValor = null,
        public readonly ?Pagador $pagador = null,
        public readonly ?Devedor $devedor = null,
        #[ArrayOf(Devolucao::class)] public readonly array $devolucoes = [],
    ) {}
}
