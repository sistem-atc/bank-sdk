<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Bolecode;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Bolecode\BolecodeResponse;

/**
 * Bolecode Pix — produto "Bolecode Pix" do portal Itaú (boleto híbrido: registra
 * um boleto e associa um QR Code Pix na mesma emissão).
 *
 * Base path (host + prefixo da API de recebimentos): `/recebimentos-pix/v1`.
 * Único endpoint de cliente documentado é a emissão `POST /boletos_pix`; a
 * "Notificação de novo consentimento" da spec é um webhook INBOUND (o parceiro
 * expõe a URL de callback), portanto não vira método de cliente aqui.
 *
 * REGISTRA uma cobrança (não movimenta dinheiro na emissão). Pode responder 200
 * (bolecode pronto) ou 202 (processamento assíncrono — reconsultar depois).
 */
final class BolecodeMethods extends BaseMethods
{
    private const BASE = '/recebimentos-pix/v1';

    private const PATH_EMISSAO = self::BASE.'/boletos_pix';

    /**
     * Emite um Bolecode (boleto + QR Code Pix) — `POST /boletos_pix`.
     *
     * O contrato do Itaú embrulha o payload em `{"data": {...}}`; passe aqui só o
     * conteúdo interno (etapa_processo_boleto, beneficiario, dado_boleto...) que o
     * método envelopa. Use `etapa_processo_boleto = 'validacao'` pra simular a
     * emissão e `'efetivacao'` pra registrar de fato.
     *
     * @param  array<string, mixed>  $dados  Conteúdo interno do boleto (sem o `data`).
     */
    public function emitir(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_EMISSAO, body: ['data' => $dados]);

        // Sucesso (200) vem embrulhado em `data`; o 202 assíncrono vem chão
        // (`{codigo, mensagem}`) — o coalesce cobre os dois formatos.
        return BolecodeResponse::fromArray($data['data'] ?? $data);
    }
}
