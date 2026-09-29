<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PagStream;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\PagBrasil\Bases\RestMethods;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ProductFilter;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\ProductSettings;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\Product;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\ProductList;

/**
 * Catálogo de assinatura.
 *
 *   listar      GET   /products?status&search&amount&billing_cycle&on_customer_area&single_purchase&order_trigger&page&per_page
 *   configurar  PATCH /products/{sku}
 *
 * Nome, descrição, imagem e preço vêm da loja e NÃO se editam aqui — só a
 * camada de assinatura (status, vitrine, regras de ciclo).
 */
final class ProductsMethods extends RestMethods
{
    /** @param ProductFilter|array<string, mixed> $filtros */
    public function listar(ProductFilter|array $filtros = []): ProductList
    {
        $filtros = $this->payload($filtros);

        foreach ($filtros as $name => $value) {
            if (is_bool($value)) {
                $filtros[$name] = $value ? 'true' : 'false';
            }
        }

        return ProductList::fromArray($this->request(HttpMethod::GET, '/products', query: $filtros));
    }

    /**
     * Ativa/desativa e configura o produto (parcial em todos os níveis):
     * status, on_customer_area, single_purchase, order_trigger,
     * display_order, default_rule_id, rules[{id?, billing_cycle,
     * shipping_cycle, status, limit, billing_day, cycle_turnover_day}].
     * Alternar o status emite o webhook de produto.
     *
     * @param  ProductSettings|array<string, mixed>  $dados
     */
    public function configurar(string $sku, ProductSettings|array $dados): Product
    {
        return Product::fromArray($this->request(HttpMethod::PATCH, '/products/'.$this->segment($sku), body: $this->payload($dados)));
    }
}
