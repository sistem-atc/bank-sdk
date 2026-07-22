<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Devolução de um Pix recebido — `/v2/pix/{e2eid}/devolucao/{id}`.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class Devolucao implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $rtrId = null,
        public readonly ?string $valor = null,
        public readonly ?string $natureza = null,
        public readonly ?string $descricao = null,
        public readonly ?Horario $horario = null,
        public readonly ?string $status = null,
        public readonly ?string $motivo = null,
    ) {}
}
