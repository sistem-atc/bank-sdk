<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use DateTimeInterface;
use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Address;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Card;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Product;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Nova assinatura (API v1, /api/pagstream/subscription/add). A forma de
 * cobrança sai do que vier: `card` → cartão; `pixRecId` → Pix Automático;
 * nenhum → Link de Pagamento enviado por e-mail antes de cada vencimento.
 *
 * `billingCycle`: W, M, Q, S, Y ou código próprio da conta. `limit` = máximo
 * de renovações (null/0 = sem limite).
 */
final class NewSubscription implements RequestPayload
{
    use SerializesRequest;

    /** @param list<Product> $products */
    public function __construct(
        #[ArrayOf(Product::class)]
        public readonly array $products,
        public readonly string $productName,
        #[Flatten]
        public readonly Customer $customer,
        #[Flatten]
        public readonly Address $address,
        #[Money]
        public readonly int|float|string $amountBrl,
        public readonly DateTimeInterface|string $nextBillingDate,
        public readonly string $billingCycle,
        public readonly ?int $limit = null,
        public readonly ?string $shippingCycle = null,
        #[Flatten]
        public readonly ?Card $card = null,
        #[JsonKey('cc_installments')]
        public readonly ?int $installments = null,
        public readonly ?string $pixRecId = null,
    ) {}
}
