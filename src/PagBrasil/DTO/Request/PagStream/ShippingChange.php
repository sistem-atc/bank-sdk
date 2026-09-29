<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use DateTimeInterface;
use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Ignore;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Mudança num envio pendente: cancelar, reagendar e/ou trocar os itens
 * (`items` SUBSTITUI a lista inteira). Cancelar ignora o resto.
 */
final class ShippingChange implements RequestPayload
{
    use SerializesRequest;

    /** @param list<ShippingItemInput>|null $items */
    public function __construct(
        public readonly int $shippingId,
        #[Ignore]
        public readonly bool $cancel = false,
        public readonly DateTimeInterface|string|null $scheduledFor = null,
        #[ArrayOf(ShippingItemInput::class)]
        public readonly ?array $items = null,
    ) {
        if (! $cancel && $scheduledFor === null && $items === null) {
            throw new InvalidArgumentException('PagBrasil: envio sem alteração — cancele, reagende ou troque os itens.');
        }

        if ($items === []) {
            throw new InvalidArgumentException('PagBrasil: a lista de itens do envio não pode ser vazia.');
        }
    }

    public static function cancelar(int $shippingId): self
    {
        return new self($shippingId, cancel: true);
    }

    protected function fixedFields(): array
    {
        return $this->cancel ? ['status' => 'canceled'] : [];
    }
}
