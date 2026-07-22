<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Par nome/valor de informação adicional exibida ao pagador.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class InformacaoAdicional implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nome = null,
        public readonly ?string $valor = null,
    ) {}
}
