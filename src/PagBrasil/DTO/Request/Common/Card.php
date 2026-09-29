<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Dados do cartão (cc_*). ⚠️ Só transite isto com servidor PCI-DSS (SAQ-D) e
 * NUNCA grave/logue. Sem PCI, use Link de Pagamento ou PagBrasil.JS.
 */
final class Card implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[JsonKey('cc_holder')]
        public readonly string $holder,
        #[JsonKey('cc_number')]
        public readonly string $number,
        /** MM/YY. */
        #[JsonKey('cc_expiration')]
        public readonly string $expiration,
        /** Opcional em débito (há cartão sem CVV). */
        #[JsonKey('cc_cvv')]
        public readonly ?string $cvv = null,
    ) {}

    public function __debugInfo(): array
    {
        // Nada de número/CVV em dump, log de exceção ou var_dump.
        return ['holder' => $this->holder, 'number' => '****'.substr($this->number, -4)];
    }
}
