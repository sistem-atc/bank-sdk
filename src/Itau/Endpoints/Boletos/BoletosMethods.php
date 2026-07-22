<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Boletos;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\BoletoEmissaoResponse;

/**
 * Boletos Cobrança — EMISSÃO/registro de boleto.
 * Produto do portal: "API Boletos - Emissão e Instrução".
 * Base path: `/cash_management/v2/boletos` (host padrão api.itau.com.br).
 *
 * O payload de entrada e a resposta são embrulhados em `data`. A emissão
 * efetiva usa `etapa_processo_boleto = 'efetivacao'`; a simulação, 'validacao'.
 */
final class BoletosMethods extends BaseMethods
{
    private const BASE = '/cash_management/v2/boletos';

    /**
     * Emite (registra) um boleto. Recebe o objeto de dados do boleto (o miolo
     * que a API espera dentro de `data`) e o embrulha automaticamente.
     *
     * @param  array<string, mixed>  $dados  Se já contiver a chave `data`, é enviado como está.
     */
    public function emitir(array $dados): DTOInterface
    {
        $body = array_key_exists('data', $dados) ? $dados : ['data' => $dados];

        $data = $this->makeRequest(HttpMethod::POST, self::BASE, body: $body);

        return BoletoEmissaoResponse::fromArray($data['data'] ?? $data);
    }

    /**
     * Simula a emissão (não registra): força `etapa_processo_boleto = 'validacao'`.
     *
     * @param  array<string, mixed>  $dados
     */
    public function simular(array $dados): DTOInterface
    {
        $inner = array_key_exists('data', $dados) ? (array) $dados['data'] : $dados;
        $inner['etapa_processo_boleto'] = 'validacao';

        return $this->emitir(['data' => $inner]);
    }
}
