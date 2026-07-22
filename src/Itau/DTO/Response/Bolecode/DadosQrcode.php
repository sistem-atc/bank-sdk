<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `dados_qrcode` do Bolecode Pix — dados do QR Code vinculado ao boleto.
 *
 * `chave` é a chave DICT do recebedor; `id_location` (int64) referencia a URL da
 * cobrança quando o QR Code foi gerado pela API de Location; `tipo_cobranca`
 * aceita `cob` (cobrança pix imediata).
 */
final class DadosQrcode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $chave = null,
        public readonly ?int $idLocation = null,
        public readonly ?string $tipoCobranca = null,
    ) {}
}
