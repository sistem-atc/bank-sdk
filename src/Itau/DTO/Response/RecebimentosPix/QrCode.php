<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Imagem/payload de QR Code — resposta de `GET /cob/{txid}/qrcode` e
 * `GET /cobv/{txid}/qrcode` (endpoints em obsolescência). `imagemQrcode` costuma
 * vir como data URI base64; `pixCopiaECola`/`emv` é o payload EMV textual.
 */
final class QrCode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $txid = null,
        public readonly ?string $imagemQrcode = null,
        public readonly ?string $qrcode = null,
        public readonly ?string $emv = null,
        public readonly ?string $pixCopiaECola = null,
        public readonly ?string $link = null,
    ) {}
}
