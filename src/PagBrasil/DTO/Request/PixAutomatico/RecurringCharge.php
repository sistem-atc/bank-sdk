<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico;

use DateTimeInterface;
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
 * Cobrança recorrente sobre um consentimento ativo — /api/order/add com
 * pix_rec_id. Envie de 2 a 10 dias ANTES de `recurrenceDate` (de preferência
 * antes das 21h). O valor pode variar, dentro do limite que o cliente
 * autorizou.
 */
final class RecurringCharge implements RequestPayload
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
        public readonly string $pixRecId,
        #[JsonKey('pix_rec_recurrence_date')]
        public readonly DateTimeInterface|string $recurrenceDate,
        public readonly ?string $paramUrl = null,
    ) {}

    protected function fixedFields(): array
    {
        return ['payment_method' => PaymentMethod::Pix->value];
    }
}
