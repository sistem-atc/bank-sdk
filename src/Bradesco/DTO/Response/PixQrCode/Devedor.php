<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Devedor da cobrança. Os campos de endereço/e-mail só vêm na `cobv`.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class Devedor implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $logradouro = null,
        public readonly ?string $cidade = null,
        public readonly ?string $uf = null,
        public readonly ?string $cep = null,
    ) {}
}
