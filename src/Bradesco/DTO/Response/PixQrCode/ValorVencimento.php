<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Valor da cobrança com vencimento, com seus componentes (`cobv`).
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class ValorVencimento implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $original = null,
        public readonly ?Multa $multa = null,
        public readonly ?Juros $juros = null,
        public readonly ?Abatimento $abatimento = null,
        public readonly ?Desconto $desconto = null,
    ) {}
}
