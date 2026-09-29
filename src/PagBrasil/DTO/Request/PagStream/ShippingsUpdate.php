<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;

/**
 * Alterações na agenda de envios. `renewal` é indexado pelo NÚMERO da
 * recorrência. `confirmPaidCycleEdit` precisa ser true quando os itens novos
 * custam mais que o valor atual do envio (ciclo já pago).
 */
final class ShippingsUpdate implements RequestPayload
{
    /**
     * @param  list<ShippingChange>  $firstBuy
     * @param  array<int, list<ShippingChange>>  $renewal
     */
    public function __construct(
        public readonly array $firstBuy = [],
        public readonly array $renewal = [],
        public readonly bool $confirmPaidCycleEdit = false,
    ) {
        if ($firstBuy === [] && $renewal === []) {
            throw new InvalidArgumentException('PagBrasil: nada a alterar nos envios.');
        }
    }

    public function toArray(): array
    {
        $body = [];

        if ($this->firstBuy !== []) {
            $body['first_buy_shippings'] = array_map(fn (ShippingChange $c) => $c->toArray(), $this->firstBuy);
        }

        if ($this->renewal !== []) {
            $renewal = [];
            foreach ($this->renewal as $recurrence => $changes) {
                $renewal[(string) $recurrence] = array_map(fn (ShippingChange $c) => $c->toArray(), $changes);
            }
            $body['renewal_shippings'] = $renewal;
        }

        if ($this->confirmPaidCycleEdit) {
            $body['confirm_paid_cycle_edit'] = true;
        }

        return $body;
    }
}
