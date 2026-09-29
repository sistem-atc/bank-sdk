<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\Payout;

use DateTimeInterface;
use SistemAtc\Banks\PagBrasil\Bases\FormMethods;
use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Request\Payout\PayeeData;
use SistemAtc\Banks\PagBrasil\DTO\Response\Payout\PayeeResponse;
use SistemAtc\Banks\PagBrasil\DTO\Response\Payout\PayoutResponse;

/**
 * PagBrasil Payout — POST /api/payout/ com `action`:
 *
 *   cadastrarFavorecido  addpayee      atualizarFavorecido  updatepayee
 *   removerFavorecido    deletepayee   consultarFavorecido  getpayee
 *   enviar               addpayout
 *
 * O favorecido é identificado pelo CPF/CNPJ (payee_taxid). Nome e documento
 * não mudam depois do cadastro: pra corrigir, remova e cadastre de novo. O
 * favorecido passa por aprovação (payee.status 0→1/2), avisada por webhook;
 * o payout também é assíncrono (webhook successpayout/failpayout).
 *
 * Resposta com success=false vira PagBrasilRequestException com as
 * mensagens de `error_message` (separadas por ";").
 *
 * Sandbox: favorecido com CPF 91051605962 tem payout concluído
 * automaticamente; outro documento (ex.: CNPJ 78797547000157) falha.
 */
final class PayoutMethods extends FormMethods
{
    private const PATH = '/api/payout/';

    /**
     * Campos: payee_name, payee_taxid, payee_bank (código COMPE), payee_branch
     * (sem dígito), payee_account (com hífen e dígito, "1234568-0"),
     * payee_account_type (1 corrente, 2 poupança), payee_description,
     * payee_email, payee_phone, payee_street, payee_zip, payee_city,
     * payee_state e opcional payee_document_link.
     *
     * @param  PayeeData|array<string, mixed>  $dados
     */
    public function cadastrarFavorecido(PayeeData|array $dados): PayeeResponse
    {
        if ($dados instanceof PayeeData && ($missing = $dados->missingForCreate()) !== []) {
            throw new InvalidArgumentException('PagBrasil: cadastro de favorecido sem '.implode(', ', $missing).'.');
        }

        return $this->payee('addpayee', $this->payload($dados));
    }

    /**
     * Atualiza os campos enviados (payee_taxid identifica, os demais são
     * opcionais).
     *
     * @param  PayeeData|array<string, mixed>  $dados
     */
    public function atualizarFavorecido(PayeeData|array $dados): PayeeResponse
    {
        return $this->payee('updatepayee', $this->payload($dados));
    }

    public function removerFavorecido(string $payeeTaxid): PayeeResponse
    {
        return $this->payee('deletepayee', ['payee_taxid' => $payeeTaxid]);
    }

    public function consultarFavorecido(string $payeeTaxid): PayeeResponse
    {
        return $this->payee('getpayee', ['payee_taxid' => $payeeTaxid], idempotent: true);
    }

    /**
     * Envia um payout ao favorecido (payee_id = o CPF/CNPJ cadastrado; entre
     * contas PagBrasil, o pbtoken da conta destino). NÃO é repetido
     * automaticamente em falha de rede — consulte antes de reenviar.
     */
    public function enviar(
        string $payeeId,
        float|string $valor,
        ?string $descricao = null,
        DateTimeInterface|string|null $data = null,
    ): PayoutResponse {
        $parsed = $this->post(self::PATH, [
            'action' => 'addpayout',
            'payee_id' => $payeeId,
            'payout_amount' => $valor,
            'payout_description' => $descricao,
            'payout_date' => $data instanceof DateTimeInterface ? $data->format('Y-m-d') : $data,
        ]);

        return PayoutResponse::fromArray($this->successful($this->expectStructure($parsed)));
    }

    /** @param array<string, mixed> $params */
    private function payee(string $action, array $params, bool $idempotent = false): PayeeResponse
    {
        $parsed = $this->post(self::PATH, ['action' => $action] + $params, $idempotent);

        return PayeeResponse::fromArray($this->successful($this->expectStructure($parsed)));
    }

    /**
     * `success` vem como "true"/"false" (string, no XML) ou booleano (JSON) —
     * normaliza antes de hidratar, senão (bool) "false" daria true.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function successful(array $data): array
    {
        $success = $data['success'] ?? null;
        $data['success'] = $success === true || in_array(strtolower(trim((string) $success)), ['true', '1'], true);

        if (! $data['success']) {
            $detail = trim((string) ($data['error_message'] ?? '')) ?: 'Operação de Payout recusada.';
            $this->fail($this->lastResponse(), $detail);
        }

        return $data;
    }
}
