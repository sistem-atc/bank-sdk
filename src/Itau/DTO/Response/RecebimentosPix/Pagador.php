<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `pix.pagador` — dados da contraparte que efetuou o pagamento. Só é
 * enviado no Webhook Exclusivo (funcionalidade contratada à parte).
 */
final class Pagador implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $documento = null,
        public readonly ?string $nome = null,
        public readonly ?string $instituicao = null,
        public readonly ?string $ispb = null,
    ) {}
}
