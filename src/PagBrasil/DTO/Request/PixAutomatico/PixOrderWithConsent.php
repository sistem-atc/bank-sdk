<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico;

use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\AutomaticPixTerms;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\PixOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Pagamento Pix imediato + consentimento pras próximas cobranças, no mesmo QR
 * Code (jornada 3) — /api/order/add com pix_rec=1.
 */
final class PixOrderWithConsent implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[Flatten]
        public readonly PixOrder $order,
        #[Flatten]
        public readonly AutomaticPixTerms $terms,
    ) {}

    protected function fixedFields(): array
    {
        return ['pix_rec' => '1'];
    }
}
