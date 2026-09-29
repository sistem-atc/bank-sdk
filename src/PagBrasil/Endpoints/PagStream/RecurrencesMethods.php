<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PagStream;

use DateTimeInterface;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\PagBrasil\Bases\RestMethods;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\RecurrenceItem;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\RecurrenceItemChange;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\ChargeAttemptList;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\ChargeQueued;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\Recurrence;

/**
 * Recorrências (ciclos) de uma assinatura — mexem em UM ciclo só.
 * `$recorrencia` é o número (≥ 0) ou 'next' (a pendente de vencimento mais
 * próximo que não esteja pulada nem pausada; sem ela → 409
 * no_pending_recurrence). tentativas() não aceita 'next'.
 *
 *   adicionarItem     POST   …/recurrences/{r}/items
 *   alterarItem       PATCH  …/recurrences/{r}/items/{sku}
 *   removerItem       DELETE …/recurrences/{r}/items/{sku}
 *   substituirItens   POST   …/recurrences/{r}/items/replace
 *   aplicarDesconto   PATCH  …/recurrences/{r}/discount
 *   reagendar         PATCH  …/recurrences/{r}
 *   cobrar            POST   …/recurrences/{r}/charge          (202, assíncrono)
 *   tentativas        GET    …/recurrences/{r}/charge-attempts
 *   pular / despular  POST   …/recurrences/{r}/skip | /unskip
 *
 * Nenhuma dispara webhook, exceto pular/despular.
 */
final class RecurrencesMethods extends RestMethods
{
    /**
     * Compra avulsa só nesta recorrência. Dados: product_sku, quantity
     * (inteiro), price (string decimal, opcional = preço do catálogo),
     * discount (percentual "10.00", opcional).
     *
     * @param  RecurrenceItem|array<string, mixed>  $dados
     */
    public function adicionarItem(string $assinatura, int|string $recorrencia, RecurrenceItem|array $dados): Recurrence
    {
        return $this->snapshot(HttpMethod::POST, $assinatura, $recorrencia, '/items', $this->payload($dados));
    }

    /**
     * Altera quantity / price / discount de uma linha (ao menos um).
     *
     * @param  RecurrenceItemChange|array<string, mixed>  $dados
     */
    public function alterarItem(string $assinatura, int|string $recorrencia, string $sku, RecurrenceItemChange|array $dados): Recurrence
    {
        return $this->snapshot(HttpMethod::PATCH, $assinatura, $recorrencia, '/items/'.$this->segment($sku), $this->payload($dados));
    }

    /** Remove a linha só desta recorrência (volta na próxima). */
    public function removerItem(string $assinatura, int|string $recorrencia, string $sku): Recurrence
    {
        return $this->snapshot(HttpMethod::DELETE, $assinatura, $recorrencia, '/items/'.$this->segment($sku));
    }

    /**
     * Troca SKUs atomicamente, mantendo quantidade/preço/desconto da linha
     * antiga: ['CAFE-500G' => 'CAFE-1KG', …] (máx. 100 pares).
     *
     * @param  array<string, string>  $pares  sku antigo => sku novo
     */
    public function substituirItens(string $assinatura, int|string $recorrencia, array $pares): Recurrence
    {
        $body = [];
        foreach ($pares as $old => $new) {
            $body[] = ['old' => (string) $old, 'new' => $new];
        }

        return $this->snapshot(HttpMethod::POST, $assinatura, $recorrencia, '/items/replace', $body);
    }

    /**
     * Desconto percentual da recorrência inteira ("15.00" = 15%; "0" remove;
     * "100.00" isenta e conclui o ciclo).
     */
    public function aplicarDesconto(string $assinatura, int|string $recorrencia, string $percentual): Recurrence
    {
        return $this->snapshot(HttpMethod::PATCH, $assinatura, $recorrencia, '/discount', ['discount' => $percentual]);
    }

    /**
     * Move a data de renovação. ⚠️ `$sobrescreverCiclo = true` é DESTRUTIVO:
     * fixa o dia como dia de cobrança e APAGA as recorrências pendentes
     * seguintes. Limpa o consentimento Pix Automático em ambos os modos.
     */
    public function reagendar(
        string $assinatura,
        int|string $recorrencia,
        DateTimeInterface|string $data,
        bool $sobrescreverCiclo = false,
    ): Recurrence {
        return $this->snapshot(HttpMethod::PATCH, $assinatura, $recorrencia, '', [
            'renewal_date' => $data instanceof DateTimeInterface ? $data->format('Y-m-d') : $data,
            'overwrite_cycle' => $sobrescreverCiclo,
        ]);
    }

    /**
     * Cobra já, pela forma de pagamento da assinatura (Pix Automático é
     * recusado). Assíncrono: acompanhe em tentativas().
     */
    public function cobrar(string $assinatura, int|string $recorrencia): ChargeQueued
    {
        return ChargeQueued::fromArray($this->request(HttpMethod::POST, $this->path($assinatura, $recorrencia, '/charge')));
    }

    /** Histórico de tentativas de cobrança da recorrência (mais antiga primeiro). */
    public function tentativas(string $assinatura, int $recorrencia): ChargeAttemptList
    {
        return ChargeAttemptList::fromArray($this->request(HttpMethod::GET, $this->path($assinatura, $recorrencia, '/charge-attempts')));
    }

    /** Marca a recorrência como pulada (não cobra). Emite webhook. */
    public function pular(string $assinatura, int|string $recorrencia): Recurrence
    {
        return $this->snapshot(HttpMethod::POST, $assinatura, $recorrencia, '/skip');
    }

    /** Desfaz o pulo — use o número; 'next' nunca resolve recorrência pulada. */
    public function despular(string $assinatura, int $recorrencia): Recurrence
    {
        return $this->snapshot(HttpMethod::POST, $assinatura, $recorrencia, '/unskip');
    }

    /** @param array<int|string, mixed>|null $body */
    private function snapshot(HttpMethod $method, string $assinatura, int|string $recorrencia, string $suffix, ?array $body = null): Recurrence
    {
        return Recurrence::fromArray($this->request($method, $this->path($assinatura, $recorrencia, $suffix), body: $body));
    }

    private function path(string $assinatura, int|string $recorrencia, string $suffix): string
    {
        return '/subscriptions/'.$this->segment($assinatura).'/recurrences/'.$this->recurrence($recorrencia).$suffix;
    }
}
