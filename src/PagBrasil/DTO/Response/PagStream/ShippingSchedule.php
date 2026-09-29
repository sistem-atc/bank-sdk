<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Agenda de envios de uma assinatura (GET/PATCH …/shippings), inteira e sem
 * paginação:
 *
 *   - firstBuyShippings: envios da primeira compra (valores cheios, sem desconto);
 *   - renewalShippings: envios de cada renovação, indexados pelo número da
 *     recorrência (valores líquidos do desconto da recorrência).
 *
 * Hidratação manual: `renewal_shippings` é um MAPA recorrência → lista, forma
 * que o AutoHydrate não descreve.
 */
final class ShippingSchedule implements DTOInterface
{
    /**
     * @param  list<Shipping>  $firstBuyShippings
     * @param  array<int, list<Shipping>>  $renewalShippings
     */
    public function __construct(
        public readonly array $firstBuyShippings = [],
        public readonly array $renewalShippings = [],
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        $firstBuy = array_map(
            fn (array $s) => Shipping::fromArray($s),
            array_values(array_filter((array) ($data['first_buy_shippings'] ?? []), 'is_array')),
        );

        $renewals = [];
        foreach ((array) ($data['renewal_shippings'] ?? []) as $recurrence => $shippings) {
            $renewals[(int) $recurrence] = array_map(
                fn (array $s) => Shipping::fromArray($s),
                array_values(array_filter((array) $shippings, 'is_array')),
            );
        }

        return new static($firstBuy, $renewals);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $renewals = [];
        foreach ($this->renewalShippings as $recurrence => $shippings) {
            $renewals[(string) $recurrence] = array_map(fn (Shipping $s) => $s->toArray(), $shippings);
        }

        return [
            'first_buy_shippings' => array_map(fn (Shipping $s) => $s->toArray(), $this->firstBuyShippings),
            'renewal_shippings' => $renewals,
        ];
    }
}
