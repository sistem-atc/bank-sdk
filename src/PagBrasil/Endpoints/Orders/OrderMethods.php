<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\Orders;

use SistemAtc\Banks\PagBrasil\Bases\FormMethods;
use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\RefundBankAccount;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\RefundPixKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\BoletoOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\CardOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\DebitOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\PixOrder;
use SistemAtc\Banks\PagBrasil\DTO\Request\Orders\Refund;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Response\Orders\Order;
use SistemAtc\Banks\PagBrasil\DTO\Response\Orders\PaymentUrl;
use SistemAtc\Banks\PagBrasil\Enums\CaptureMode;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;

/**
 * Pedidos da API clássica da PagBrasil — o mesmo conjunto de rotas atende
 * cartão de crédito, Débito Flash, boleto/Boleto Flash e Pix; quem escolhe é
 * o `payment_method` (C, D, B, X).
 *
 *   criar/cartao/debito/boleto/pix  POST /api/order/add
 *   capturar                        POST /api/order/add (cc_auth=2)
 *   consultar                       POST /api/order/get
 *   estornar                        POST /api/order/refund
 *   prorrogarBoleto / prorrogarPix  POST /api/order/extend
 *   cancelar                        POST /api/order/cancel
 *   excluirCartaoSalvo              POST /api/order/creditcard/delete
 *   pixUmClique                     POST /api/pix/1click
 *
 * Entrada: o DTO do meio (CardOrder, DebitOrder, BoletoOrder, PixOrder,
 * Refund) ou, por compatibilidade, o array com os nomes da doc (order,
 * customer_taxid, amount_brl…; `products` como array vira o JSON da API).
 *
 * ⚠️ Idempotência: o `order` é a chave. Reenviar o mesmo order + customer_taxid
 * de um pedido NÃO pago atualiza e retenta; de um pedido pago, ou com outro
 * CPF/CNPJ, dá "Duplicated order.". Se o add ficar sem resposta, CONSULTE
 * antes de reenviar (a doc alerta pra cobrança duplicada) — por isso o SDK
 * não repete o add sozinho.
 *
 * ⚠️ PCI: mandar número de cartão por aqui exige servidor PCI-DSS (SAQ-D).
 * Sem isso, use o Link de Pagamento ou o PagBrasil.JS (token via auth()).
 */
final class OrderMethods extends FormMethods
{
    /**
     * Cria um pedido com o `payment_method` informado nos dados. O retorno é
     * o pedido já processado: cartão aprovado/recusado na hora (ou WP se foi
     * pra revisão manual — aí o resultado chega por IPN), boleto com
     * `url_boleto`, Pix com `pix_image`/`pix_code`.
     *
     * @param  RequestPayload|array<string, mixed>  $dados
     */
    public function criar(RequestPayload|array $dados): Order
    {
        return $this->order('/api/order/add', $this->payload($dados));
    }

    /**
     * Cartão de crédito (payment_method=C). `cc_auth` default 0 (autoriza e
     * captura); `cc_save` default 0 — só use 1 com autorização EXPLÍCITA do
     * cliente pra cobranças futuras.
     *
     * @param  CardOrder|array<string, mixed>  $dados
     */
    public function cartao(CardOrder|array $dados): Order
    {
        if ($dados instanceof CardOrder) {
            return $this->criar($dados);
        }

        return $this->criar(['payment_method' => PaymentMethod::CreditCard->value, 'cc_save' => '0', 'cc_auth' => CaptureMode::AuthorizeAndCapture->value] + $dados);
    }

    /**
     * Débito Flash (payment_method=D) — captura de transação autenticada no
     * PagBrasil.JS (campos auth3ds_*). Sempre 1 parcela.
     *
     * @param  DebitOrder|array<string, mixed>  $dados
     */
    public function debito(DebitOrder|array $dados): Order
    {
        if ($dados instanceof DebitOrder) {
            return $this->criar($dados);
        }

        return $this->criar(['payment_method' => PaymentMethod::DebitCard->value, 'cc_installments' => '1'] + $dados);
    }

    /**
     * Boleto (payment_method=B; sai como Boleto Flash se a conta tiver o
     * produto). `bol_expiration` em dias (0–999).
     *
     * @param  BoletoOrder|array<string, mixed>  $dados
     */
    public function boleto(BoletoOrder|array $dados): Order
    {
        if ($dados instanceof BoletoOrder) {
            return $this->criar($dados);
        }

        return $this->criar(['payment_method' => PaymentMethod::Boleto->value] + $dados);
    }

    /**
     * Pix (payment_method=X). `pix_expiration` em minutos (1–7200). Para
     * usar o 1-Click Pix depois, mande também `url_return`.
     *
     * @param  PixOrder|array<string, mixed>  $dados
     */
    public function pix(PixOrder|array $dados): Order
    {
        if ($dados instanceof PixOrder) {
            return $this->criar($dados);
        }

        return $this->criar(['payment_method' => PaymentMethod::Pix->value] + $dados);
    }

    /**
     * Captura (total ou parcial) de uma pré-autorização de cartão feita com
     * cc_auth=1. Valor ≤ o autorizado, até 14 dias depois.
     */
    public function capturar(string $order, float|string $valor): Order
    {
        return $this->criar([
            'order' => $order,
            'payment_method' => PaymentMethod::CreditCard->value,
            'amount_brl' => $valor,
            'cc_auth' => CaptureMode::Capture->value,
        ]);
    }

    /** Situação do pedido. null quando o pedido não existe na PagBrasil. */
    public function consultar(string $order): ?Order
    {
        $parsed = $this->post('/api/order/get', ['order' => $order], idempotent: true);
        $data = $this->expectStructure($parsed, allowEmpty: true);

        return $data === [] ? null : Order::fromArray($data);
    }

    /**
     * Solicita estorno (total ou parcial). Processado em até 1 dia útil; a
     * confirmação chega por IPN (payment_status P = processado, J =
     * rejeitado). Retorna o texto de confirmação da PagBrasil.
     *
     * Estorno rejeitado (cartão com +300 dias, Pix com +90 dias…) é refeito
     * com o destino do CLIENTE: RefundBankAccount ou RefundPixKey (ou, em
     * array, customer_bank/_branch/_account ou customer_pix_key_type/_key).
     *
     *   estornar(new Refund('PED-1', '50.00', new RefundPixKey(PixKeyType::Email, 'a@b.com')))
     *   estornar('PED-1', '50.00')
     *
     * @param  RefundBankAccount|RefundPixKey|array<string, mixed>  $destino
     */
    public function estornar(
        Refund|string $pedido,
        float|string|null $valor = null,
        RefundBankAccount|RefundPixKey|array $destino = [],
    ): string {
        if (! $pedido instanceof Refund) {
            if ($valor === null) {
                throw new InvalidArgumentException('PagBrasil: informe o valor do estorno.');
            }

            $params = ['order' => $pedido, 'amount_refunded' => $valor] + $this->payload($destino);
        }

        $parsed = $this->post('/api/order/refund', $params ?? $pedido->toArray());

        return $this->expectText($parsed, 'Refund request received');
    }

    /**
     * Prorroga o vencimento de um boleto em N dias. Retorna a URL do NOVO
     * boleto — é ela que se manda ao cliente.
     */
    public function prorrogarBoleto(string $order, int $dias): string
    {
        $parsed = $this->post('/api/order/extend', ['order' => $order, 'extend_days' => $dias]);

        // A doc manda validar pelo prefixo: começou com "http" é a URL;
        // qualquer outra coisa é mensagem de erro.
        return $this->expectText($parsed, 'http');
    }

    /** Prorroga a validade de um Pix em N minutos (180–7200). */
    public function prorrogarPix(string $order, int $minutos): Order
    {
        return $this->order('/api/order/extend', ['order' => $order, 'extend_minutes' => $minutos]);
    }

    /**
     * Cancela um Pix ou boleto ainda não pago (antes do vencimento). Cancelado
     * = order_status PR. Não impede pagamento que o cliente já agendou no banco.
     */
    public function cancelar(string $order): Order
    {
        return $this->order('/api/order/cancel', ['order' => $order]);
    }

    /**
     * Apaga da PagBrasil o cartão salvo (cc_save=1) de um pedido original —
     * obrigatório quando o cliente pede pra não ser mais cobrado.
     */
    public function excluirCartaoSalvo(string $orderOriginal, string $customerTaxid): string
    {
        $parsed = $this->post('/api/order/creditcard/delete', [
            'order' => $orderOriginal,
            'customer_taxid' => $customerTaxid,
        ]);

        return $this->expectText($parsed, 'Credit card information successfully deleted');
    }

    /**
     * 1-Click Pix: URL de pagamento para um pedido Pix já criado com
     * `url_return`. Redirecione o cliente pra ela; em erro, a PagBrasil
     * devolve o cliente pro url_return com `pb_error=1`.
     */
    public function pixUmClique(string $order): PaymentUrl
    {
        $parsed = $this->post('/api/pix/1click', ['order' => $order]);
        $data = $this->expectStructure($parsed);

        if (! isset($data['url_payment']) || ! is_string($data['url_payment']) || $data['url_payment'] === '') {
            $this->fail($this->lastResponse(), 'Resposta do 1-Click Pix sem url_payment.');
        }

        return PaymentUrl::fromArray($data);
    }

    /** @param array<string, mixed> $params */
    private function order(string $path, array $params): Order
    {
        return Order::fromArray($this->expectStructure($this->post($path, $params)));
    }
}
