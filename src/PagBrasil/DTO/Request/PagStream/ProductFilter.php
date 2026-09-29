<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Ignore;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Filtro de GET /products. `status`: active, inactive ou all.
 * `billingCycles`: códigos de ciclo (casa qualquer um).
 */
final class ProductFilter implements RequestPayload
{
    use SerializesRequest;

    /** @param list<string> $billingCycles */
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $search = null,
        #[Money]
        public readonly int|float|string|null $amount = null,
        #[Ignore]
        public readonly array $billingCycles = [],
        #[Ignore]
        public readonly ?bool $onCustomerArea = null,
        #[Ignore]
        public readonly ?bool $singlePurchase = null,
        #[Ignore]
        public readonly ?bool $orderTrigger = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
    ) {}

    protected function fixedFields(): array
    {
        $fields = $this->billingCycles === [] ? [] : ['billing_cycle' => implode(',', $this->billingCycles)];

        // Na query string, booleano vai como "true"/"false".
        foreach (['on_customer_area' => $this->onCustomerArea, 'single_purchase' => $this->singlePurchase, 'order_trigger' => $this->orderTrigger] as $key => $flag) {
            if ($flag !== null) {
                $fields[$key] = $flag ? 'true' : 'false';
            }
        }

        return $fields;
    }
}
