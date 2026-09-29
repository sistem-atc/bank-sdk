<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Orders;

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
 * Boleto (/api/order/add, payment_method=B; sai Boleto Flash se a conta
 * tiver o produto). `expirationDays` 0–999 (null = default do Dashboard).
 * `storeCode` só pra conta com mais de uma loja (logo/mensagem do boleto).
 */
final class BoletoOrder implements RequestPayload
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
        #[JsonKey('bol_expiration')]
        public readonly ?int $expirationDays = null,
        public readonly ?string $storeCode = null,
        public readonly ?string $paramUrl = null,
    ) {}

    protected function fixedFields(): array
    {
        return ['payment_method' => PaymentMethod::Boleto->value];
    }
}
