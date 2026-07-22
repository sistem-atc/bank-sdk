<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Eco dos filtros usados na listagem + paginação (padrão Bacen, comum a cob/cobv/loc/pix/webhook).
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class Parametros implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $inicio = null,
        public readonly ?string $fim = null,
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $txid = null,
        public readonly ?bool $txIdPresente = null,
        public readonly ?bool $locationPresente = null,
        public readonly ?bool $devolucaoPresente = null,
        public readonly ?string $tipoCob = null,
        public readonly ?string $status = null,
        public readonly ?string $loteCobVId = null,
        public readonly ?Paginacao $paginacao = null,
    ) {}
}
