<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Ignore;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Regra de ciclo de um produto. Sem `id` = regra nova (aí `billingCycle` é
 * obrigatório). Campo null = manter o atual; pra APAGAR o ciclo de envio ou o
 * dia fixo use clearShippingCycle/clearBillingDay (a API distingue ausente de
 * null). Regra não se apaga — desative com status "inactive".
 */
final class ProductRuleSettings implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $billingCycle = null,
        public readonly ?string $shippingCycle = null,
        /** "active" ou "inactive". */
        public readonly ?string $status = null,
        public readonly ?int $limit = null,
        public readonly ?int $billingDay = null,
        public readonly ?int $cycleTurnoverDay = null,
        #[Ignore]
        public readonly bool $clearShippingCycle = false,
        #[Ignore]
        public readonly bool $clearBillingDay = false,
    ) {
        if ($id === null && $billingCycle === null) {
            throw new InvalidArgumentException('PagBrasil: regra nova exige billingCycle.');
        }

        if ($billingDay !== null && ($billingDay < 1 || $billingDay > 28)) {
            throw new InvalidArgumentException('PagBrasil: dia de cobrança de 1 a 28.');
        }
    }

    protected function fixedFields(): array
    {
        // null EXPLÍCITO no JSON = apagar (ausente = manter).
        $fields = [];

        if ($this->clearShippingCycle) {
            $fields['shipping_cycle'] = null;
        }

        if ($this->clearBillingDay) {
            $fields['billing_day'] = null;
        }

        return $fields;
    }
}
