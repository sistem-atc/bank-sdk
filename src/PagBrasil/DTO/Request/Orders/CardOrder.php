<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Orders;

use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Address;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Card;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Product;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\ThreeDSecure;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Wallet;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\CaptureMode;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;

/**
 * Pedido de cartão de crédito (/api/order/add, payment_method=C).
 *
 * O cartão vem de UMA fonte: `card` (dados abertos, exige PCI), `wallet`
 * (Apple/Google/Samsung Pay) ou `previousOrder` (cartão salvo antes com
 * `saveCard: true` — o valor é o número daquele pedido original).
 *
 * `saveCard` só com autorização EXPLÍCITA do cliente pra cobranças futuras
 * (e a doc obriga a avisar o cliente por e-mail 5–15 dias antes de cada
 * cobrança recorrente). `capture`: autoriza+captura (default) ou só
 * pré-autoriza por 14 dias — a captura é pedidos()->capturar().
 * Parcela mínima de R$ 5,00.
 */
final class CardOrder implements RequestPayload
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
        #[JsonKey('cc_installments')]
        public readonly int $installments = 1,
        #[Flatten]
        public readonly ?Card $card = null,
        #[Flatten]
        public readonly ?Wallet $wallet = null,
        #[JsonKey('cc_prevsaved')]
        public readonly ?string $previousOrder = null,
        #[JsonKey('cc_save')]
        public readonly bool $saveCard = false,
        #[JsonKey('cc_auth')]
        public readonly CaptureMode $capture = CaptureMode::AuthorizeAndCapture,
        /** Até 13 caracteres [A-Za-z0-9 ]; aparece na fatura como "PB*…". */
        public readonly ?string $softDescriptor = null,
        #[Flatten]
        public readonly ?ThreeDSecure $threeDSecure = null,
        /** Só com a antifraude PagShield. */
        public readonly ?string $visitorId = null,
        public readonly ?string $paramUrl = null,
    ) {
        $sources = count(array_filter([$card, $wallet, $previousOrder], fn ($s) => $s !== null));

        if ($sources !== 1) {
            throw new InvalidArgumentException('PagBrasil: informe exatamente UMA fonte do cartão — card, wallet ou previousOrder.');
        }

        if ($installments < 1 || $installments > 12) {
            throw new InvalidArgumentException('PagBrasil: parcelas de 1 a 12.');
        }

        if ($capture === CaptureMode::Capture) {
            throw new InvalidArgumentException('PagBrasil: captura de pré-autorização é pedidos()->capturar().');
        }
    }

    protected function fixedFields(): array
    {
        return ['payment_method' => PaymentMethod::CreditCard->value];
    }
}
