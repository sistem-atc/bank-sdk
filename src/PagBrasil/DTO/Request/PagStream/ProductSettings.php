<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Configuração de um produto no catálogo de assinatura (PATCH parcial: null =
 * manter). Nome, preço e imagem vêm da loja e não se editam aqui.
 */
final class ProductSettings implements RequestPayload
{
    use SerializesRequest;

    /** @param list<ProductRuleSettings>|null $rules */
    public function __construct(
        /** "active" ou "inactive". */
        public readonly ?string $status = null,
        public readonly ?bool $onCustomerArea = null,
        public readonly ?bool $singlePurchase = null,
        public readonly ?bool $orderTrigger = null,
        public readonly ?int $displayOrder = null,
        public readonly ?int $defaultRuleId = null,
        #[ArrayOf(ProductRuleSettings::class)]
        public readonly ?array $rules = null,
    ) {}
}
