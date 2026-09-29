<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Orders;

use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Address;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Product;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;

/**
 * Pix (/api/order/add, payment_method=X). `expirationMinutes` 1–7200 (null =
 * default do Dashboard). `urlReturn` é obrigatório se o pedido for usar o
 * 1-Click Pix depois.
 */
final class PixOrder implements RequestPayload
{
    use SerializesRequest;

    /** @param list<Product> $products */
    public function __construct(
        public readonly string $order,
        public readonly string $productName,
        #[ArrayOf(Product::class)]
        public readonly array $products,
        #[Flatten]
        public readonly Customer $customer,
        #[Flatten]
        public readonly Address $address,
        #[Money]
        public readonly int|float|string $amountBrl,
        #[JsonKey('pix_expiration')]
        public readonly ?int $expirationMinutes = null,
        public readonly ?string $urlReturn = null,
        public readonly ?string $paramUrl = null,
    ) {
        if ($expirationMinutes !== null && ($expirationMinutes < 1 || $expirationMinutes > 7200)) {
            throw new InvalidArgumentException('PagBrasil: validade do Pix de 1 a 7200 minutos.');
        }
    }

    protected function fixedFields(): array
    {
        return ['payment_method' => PaymentMethod::Pix->value];
    }
}
