<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PixAutomatico;

use DateTimeInterface;
use SistemAtc\Banks\PagBrasil\Bases\FormMethods;
use SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico\AutomaticPixConsent;
use SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico\PixOrderWithConsent;
use SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico\RecurringCharge;
use SistemAtc\Banks\PagBrasil\DTO\Response\ApiResult;
use SistemAtc\Banks\PagBrasil\DTO\Response\Orders\Order;
use SistemAtc\Banks\PagBrasil\DTO\Response\PixAutomatico\Recurrence;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\PagBrasil\Support\PagBrasilHosts;

/**
 * Pix Automático da PagBrasil (jornadas 2 e 3 do Bacen).
 *
 *   pagamentoComConsentimento  POST /api/order/add (pix_rec=1)          jornada 3: paga agora + autoriza as próximas
 *   consentimento              POST /api/pix/rec/add                     jornada 2: só autoriza
 *   cobrar                     POST /api/order/add (pix_rec_id)          cobrança recorrente agendada
 *   retentar                   POST /api/pix/rec/retry                   nova tentativa no dia seguinte
 *   consultarRecorrencia       POST /api/pix/rec/get                     status do consentimento
 *   emularCobrancas            POST /mock/pix/automatic/chargeRecurrences (SÓ sandbox)
 *
 * Regras do arranjo que a API não perdoa:
 *   - a cobrança recorrente vai de 2 a 10 dias ANTES da data (de preferência
 *     antes das 21h);
 *   - pix_rec_first_recurrence ≥ 2 dias úteis depois do pagamento/autorização;
 *   - retentativa só se o consentimento nasceu com pix_rec_retry=1: até 3
 *     tentativas por ciclo, em dias diferentes, dentro de 7 dias, mesmo valor.
 *
 * O status do consentimento chega pelo webhook `action=consent`; se não vier
 * até D+1 do pagamento, consulte com consultarRecorrencia().
 */
final class PixAutomaticoMethods extends FormMethods
{
    /**
     * Jornada 3 — pagamento imediato + consentimento no mesmo QR Code. Além
     * dos campos de um Pix comum: pix_rec_cycle, pix_rec_first_recurrence
     * (Y-m-d), e opcionais pix_rec_description (19), pix_rec_expiration,
     * pix_rec_minimum_amount, pix_rec_retry (recomendado 1).
     *
     * @param  PixOrderWithConsent|array<string, mixed>  $dados
     */
    public function pagamentoComConsentimento(PixOrderWithConsent|array $dados): Order
    {
        return $this->order('/api/order/add', ['payment_method' => PaymentMethod::Pix->value, 'pix_rec' => '1'] + $this->payload($dados));
    }

    /**
     * Jornada 2 — só o consentimento, sem pagamento agora. Campos: payer_name,
     * payer_taxid, pix_rec_cycle, pix_rec_first_recurrence e os opcionais
     * pix_rec_description, pix_rec_expiration, pix_rec_minimum_amount,
     * pix_rec_retry. Devolve o QR Code (pix_image/pix_code) e o pix_rec_id.
     *
     * @param  AutomaticPixConsent|array<string, mixed>  $dados
     */
    public function consentimento(AutomaticPixConsent|array $dados): Order
    {
        return $this->order('/api/pix/rec/add', $this->payload($dados));
    }

    /**
     * Cobrança recorrente sobre um consentimento ativo. Além dos campos de
     * pedido Pix: pix_rec_id e pix_rec_recurrence_date (Y-m-d, 2 a 10 dias à
     * frente). O resultado final chega por IPN no dia da cobrança.
     *
     * @param  RecurringCharge|array<string, mixed>  $dados
     */
    public function cobrar(RecurringCharge|array $dados): Order
    {
        return $this->order('/api/order/add', ['payment_method' => PaymentMethod::Pix->value] + $this->payload($dados));
    }

    /**
     * Agenda nova tentativa (dia seguinte) de uma cobrança recorrente que
     * falhou. A doc não fixa o formato da resposta, então ela volta crua.
     */
    public function retentar(string $order): ApiResult
    {
        $parsed = $this->post('/api/pix/rec/retry', ['order' => $order]);

        if (! $parsed->isText()) {
            $this->verifySignature($parsed->data);
        }

        return ApiResult::fromArray([
            'format' => $parsed->format,
            'message' => $parsed->isText() ? $parsed->text() : '',
            'data' => $parsed->data,
        ]);
    }

    /** Status e parâmetros de um consentimento (Authorized, Canceled, NotInitiated). */
    public function consultarRecorrencia(string $pixRecId): Recurrence
    {
        $data = $this->expectStructure($this->post('/api/pix/rec/get', ['pix_rec_id' => $pixRecId], idempotent: true));

        // A doc grafa o campo como `pic_rec_expiration` na tabela de resposta
        // (erro de digitação dela?). Aceita as duas grafias.
        if (! isset($data['pix_rec_expiration']) && isset($data['pic_rec_expiration'])) {
            $data['pix_rec_expiration'] = $data['pic_rec_expiration'];
        }

        return Recurrence::fromArray($data);
    }

    /**
     * SANDBOX: processa todas as cobranças agendadas numa data. Valor 111.22
     * simula erro que passa na última tentativa; 111.33, erro em todas.
     */
    public function emularCobrancas(DateTimeInterface|string $data): string
    {
        if (! PagBrasilHosts::isSandbox($this->integration)) {
            throw new \BadMethodCallException('PagBrasil: o emulador de cobranças do Pix Automático só existe no sandbox.');
        }

        $parsed = $this->post('/mock/pix/automatic/chargeRecurrences', [
            'date' => $data instanceof DateTimeInterface ? $data->format('Y-m-d') : $data,
        ]);

        return $this->expectText($parsed, 'Charges processed');
    }

    /** @param array<string, mixed> $params */
    private function order(string $path, array $params): Order
    {
        return Order::fromArray($this->expectStructure($this->post($path, $params)));
    }
}
