<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionPaymentMethod;

/**
 * Troca da forma de cobrança e/ou parcelas de uma assinatura. Cartão exige
 * `cardToken` (cartão JÁ salvo do mesmo cliente); Pix Automático exige
 * `pixConsentToken`. Parcelas só com cartão, e cada parcela ≥ R$ 5,00.
 */
final class PaymentMethodChange implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[JsonKey('payment_method')]
        public readonly ?SubscriptionPaymentMethod $method = null,
        public readonly ?string $cardToken = null,
        public readonly ?string $pixConsentToken = null,
        public readonly ?int $installments = null,
    ) {
        if ($method === null && $installments === null) {
            throw new InvalidArgumentException('PagBrasil: informe a forma de pagamento e/ou as parcelas.');
        }

        if ($method === SubscriptionPaymentMethod::CreditCard && ($cardToken === null || $cardToken === '')) {
            throw new InvalidArgumentException('PagBrasil: cartão exige cardToken.');
        }

        if ($method === SubscriptionPaymentMethod::AutomaticPix && ($pixConsentToken === null || $pixConsentToken === '')) {
            throw new InvalidArgumentException('PagBrasil: Pix Automático exige pixConsentToken.');
        }
    }

    public function __debugInfo(): array
    {
        // Tokens são credencial de cobrança — nunca em dump/log.
        return ['method' => $this->method?->value, 'installments' => $this->installments];
    }
}
