<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\Checkout;

use SistemAtc\Banks\PagBrasil\Bases\FormMethods;
use SistemAtc\Banks\PagBrasil\DTO\Request\Checkout\PaymentLink;
use SistemAtc\Banks\PagBrasil\DTO\Response\Orders\PaymentUrl;

/**
 * Link de Pagamento — POST /api/checkout/add. Checkout hospedado pela
 * PagBrasil: o lojista não toca em dado de cartão (fora do escopo PCI).
 *
 * Obrigatórios: order e amount_brl. Opcionais (nomes da doc):
 *   payment_option ("C,D,B,X"), wallet_option ("AP,GP,SP"),
 *   payment_link_expiration (dias) ou payment_link_expiration_date (Y-m-d),
 *   url_return, product_name, products, address/phone/email_requested,
 *   dados do cliente pré-preenchidos, param_url, soft_descriptor, cc_auth,
 *   cc_installments, max_installments, store_code, bol_expiration,
 *   pix_expiration; 3DS (cc_/dc_authentication[_onfailure]); câmbio
 *   (fx_currency + fx_amount); Pix Automático (pix_rec=1, pix_rec_cycle,
 *   pix_rec_first_recurrence…).
 *
 * O resultado do pagamento chega pelo IPN do meio escolhido pelo cliente, e
 * o pedido é consultado normalmente por pedidos()->consultar().
 */
final class CheckoutMethods extends FormMethods
{
    /** @param PaymentLink|array<string, mixed> $dados */
    public function criar(PaymentLink|array $dados): PaymentUrl
    {
        $data = $this->expectStructure($this->post('/api/checkout/add', $this->payload($dados)));

        if (! isset($data['url_payment']) || ! is_string($data['url_payment']) || $data['url_payment'] === '') {
            $this->fail($this->lastResponse(), 'Resposta do Link de Pagamento sem url_payment.');
        }

        return PaymentUrl::fromArray($data);
    }
}
