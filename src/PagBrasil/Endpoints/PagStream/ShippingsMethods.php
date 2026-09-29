<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PagStream;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\PagBrasil\Bases\RestMethods;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ShippingFilter;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ShippingsUpdate;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\ShippingSchedule;

/**
 * Envios agendados de uma assinatura (recurso "shippings" precisa estar
 * habilitado na conta, senão 409 shippings_disabled).
 *
 *   listar   GET   /subscriptions/{n}/shippings?status&from&to
 *   alterar  PATCH /subscriptions/{n}/shippings
 */
final class ShippingsMethods extends RestMethods
{
    /**
     * Filtros: status (pending, completed, canceled), from, to (Y-m-d).
     *
     * @param  ShippingFilter|array<string, mixed>  $filtros
     */
    public function listar(string $assinatura, ShippingFilter|array $filtros = []): ShippingSchedule
    {
        return ShippingSchedule::fromArray($this->request(HttpMethod::GET, $this->path($assinatura), query: $this->payload($filtros)));
    }

    /**
     * Reagenda, troca itens (substituição TOTAL da lista) ou cancela envios
     * pendentes:
     *
     *   ['first_buy_shippings' => [['shipping_id' => 551, 'scheduled_for' => '2026-09-10']],
     *    'renewal_shippings'   => ['4' => [['shipping_id' => 902, 'status' => 'canceled']]],
     *    'confirm_paid_cycle_edit' => true]
     *
     * Cancelamento emite o webhook `shipping_canceled`.
     *
     * @param  ShippingsUpdate|array<string, mixed>  $dados
     */
    public function alterar(string $assinatura, ShippingsUpdate|array $dados): ShippingSchedule
    {
        $dados = $this->payload($dados);

        // renewal_shippings é um MAPA recorrência → lista: força objeto JSON
        // mesmo com chaves numéricas sequenciais ("0", "1"…), que o
        // json_encode transformaria em array.
        if (isset($dados['renewal_shippings']) && is_array($dados['renewal_shippings'])) {
            $dados['renewal_shippings'] = (object) $dados['renewal_shippings'];
        }

        return ShippingSchedule::fromArray($this->request(HttpMethod::PATCH, $this->path($assinatura), body: $dados));
    }

    private function path(string $assinatura): string
    {
        return '/subscriptions/'.$this->segment($assinatura).'/shippings';
    }
}
