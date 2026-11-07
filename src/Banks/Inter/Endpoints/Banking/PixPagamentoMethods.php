<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Banking;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\ConsultaPixAsyncResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\PagamentoPixResponse;
use SistemAtc\Banks\Banks\Inter\Support\Idempotency;

/**
 * Pix de SAÍDA — API Banking (`POST /banking/v2/pix`). MOVIMENTA dinheiro.
 * Escopos `pagamento-pix.write` / `pagamento-pix.read`.
 *
 * Idempotência: o `identificador` do contrato vira o header
 * `x-id-idempotente` (UUID). Se não for UUID, vira um UUID determinístico
 * (ver Support\Idempotency), então o retry do mesmo título não paga 2×.
 */
final class PixPagamentoMethods extends BaseMethods implements PixEndpoint
{
    /**
     * Paga um Pix (contrato PixEndpoint).
     *
     * Forma curta (contrato comum): `chave`, `valor`, `identificador`,
     * `descricao?`, `data_pagamento?`. Forma completa (do Inter): passe
     * `destinatario` com `tipo` CHAVE | DADOS_BANCARIOS | PIX_COPIA_E_COLA, e
     * o SDK repassa o corpo como veio.
     *
     * @param  array{identificador: string, valor: string|float, chave?: string, descricao?: string, data_pagamento?: string, dataPagamento?: string, destinatario?: array<string, mixed>}  $dados
     */
    public function pagar(array $dados): PagamentoPixResponse
    {
        if (empty($dados['identificador'])) {
            throw new \InvalidArgumentException('Inter pix()->pagar(): `identificador` (chave de idempotência) é obrigatório.');
        }

        $destinatario = $dados['destinatario'] ?? (isset($dados['chave'])
            ? ['tipo' => 'CHAVE', 'chave' => (string) $dados['chave']]
            : throw new \InvalidArgumentException('Inter pix()->pagar(): informe `chave` ou `destinatario`.'));

        $body = self::filtros([
            'valor' => (float) $dados['valor'],
            'dataPagamento' => $dados['dataPagamento'] ?? $dados['data_pagamento'] ?? null,
            'descricao' => $dados['descricao'] ?? null,
            'destinatario' => $destinatario,
        ]);

        $data = $this->makeRequest(HttpMethod::POST, '/pix', body: $body, headers: [
            'x-id-idempotente' => Idempotency::uuid((string) $dados['identificador']),
        ]);

        return PagamentoPixResponse::fromArray($data);
    }

    /**
     * Consulta o Pix pelo `codigoSolicitacao` devolvido em pagar()
     * (`GET /pix/{codigoSolicitacao}`), com o histórico de status.
     */
    public function consultar(string $identificador): ConsultaPixAsyncResponse
    {
        return ConsultaPixAsyncResponse::fromArray(
            $this->makeRequest(HttpMethod::GET, '/pix/'.rawurlencode($identificador)),
        );
    }
}
