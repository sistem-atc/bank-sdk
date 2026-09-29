<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Orders;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Address;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Card;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Product;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\ThreeDSecure;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;

/**
 * Débito Flash (/api/order/add, payment_method=D): captura de uma transação
 * JÁ autenticada no PagBrasil.JS — por isso o 3DS é obrigatório. Sempre
 * 1 parcela.
 */
final class DebitOrder implements RequestPayload
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
        #[Flatten]
        public readonly Card $card,
        #[Flatten]
        public readonly ThreeDSecure $threeDSecure,
        public readonly ?string $softDescriptor = null,
        public readonly ?string $paramUrl = null,
    ) {}

    protected function fixedFields(): array
    {
        return ['payment_method' => PaymentMethod::DebitCard->value, 'cc_installments' => '1'];
    }
}
