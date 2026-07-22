<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Cobranca;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\WebhookCobrancaResposta;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Cobrança Bradesco — cadastro de Webhook (Cobrança Convencional e/ou Híbrida).
 *
 * FAMÍLIA: open_api. Base path único:
 *   POST /boleto/cobranca-webhook/v1/cadastrar
 *
 * ⚠️ ENDPOINT ÚNICO, QUATRO OPERAÇÕES. Não existe GET/PUT/DELETE aqui: o que
 * define a operação é o campo `tipoCadastro` DO PAYLOAD:
 *
 *   | tipoCadastro | operação  | método desta classe |
 *   |--------------|-----------|---------------------|
 *   | 'I'          | Inclusão  | incluir()           |
 *   | 'A'          | Alteração | alterar()           |
 *   | 'C'          | Consulta  | consultar()         |
 *   | 'E'          | Exclusão  | excluir()           |
 *
 * Campos do payload (todos obrigatórios pela spec):
 *   - `documento`      → CPF/CNPJ do beneficiário decomposto:
 *                        {cpfCnpj (raiz, 9), filial (4, '0' se CPF), controle (2)}.
 *   - `versaoLayout`   → versão atual do layout de registro (ex.: '1').
 *   - `tipoCadastro`   → I | A | C | E (tabela acima).
 *   - `utilizaWebhook` → 'S' ativa o envio de notificações, 'N' desativa.
 *   - `urlEnvio`       → URL HTTPS que receberá a notificação (até 100 chars).
 *   - `tipoAviso`      → tipo de aviso (inteiro, 1 posição).
 *
 * Na CONSULTA e na EXCLUSÃO os campos `utilizaWebhook`/`urlEnvio` continuam
 * sendo exigidos pelo contrato — por isso os métodos nomeados também os pedem;
 * a resposta é sempre o estado do cadastro (utilizaWebhook, urlEnvio,
 * datahoraAtualizacao).
 *
 * Se precisar de um payload fora desses quatro moldes, use `cadastrar()` cru.
 */
final class CobrancaWebhookMethods extends BaseMethods
{
    private const PATH = '/boleto/cobranca-webhook/v1/cadastrar';

    /** Inclusão de cadastro de webhook. */
    public const TIPO_INCLUSAO = 'I';

    /** Alteração do cadastro existente. */
    public const TIPO_ALTERACAO = 'A';

    /** Consulta do cadastro existente. */
    public const TIPO_CONSULTA = 'C';

    /** Exclusão do cadastro. */
    public const TIPO_EXCLUSAO = 'E';

    /**
     * Chamada crua do endpoint: você monta o `RequestDTO` inteiro, inclusive o
     * `tipoCadastro` que decide a operação.
     *
     * @param  array<string, mixed>  $dados  Corpo `RequestDTO` da spec.
     */
    public function cadastrar(array $dados): WebhookCobrancaResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH, body: $dados);

        return WebhookCobrancaResposta::fromArray($data['data'] ?? $data);
    }

    /**
     * Inclui o cadastro de webhook (tipoCadastro = 'I').
     *
     * @param  array{cpfCnpj: string, filial: string, controle: string}  $documento  CPF/CNPJ decomposto do beneficiário.
     * @param  array<string, mixed>  $extras  Campos adicionais/sobrescritas do payload.
     */
    public function incluir(
        array $documento,
        string $urlEnvio,
        int $tipoAviso,
        string $utilizaWebhook = 'S',
        string $versaoLayout = '1',
        array $extras = [],
    ): WebhookCobrancaResposta {
        return $this->cadastrar($this->payload(
            self::TIPO_INCLUSAO, $documento, $urlEnvio, $tipoAviso, $utilizaWebhook, $versaoLayout, $extras,
        ));
    }

    /**
     * Altera o cadastro de webhook (tipoCadastro = 'A') — troca a URL, o tipo
     * de aviso ou liga/desliga o envio via `utilizaWebhook`.
     *
     * @param  array{cpfCnpj: string, filial: string, controle: string}  $documento  CPF/CNPJ decomposto do beneficiário.
     * @param  array<string, mixed>  $extras  Campos adicionais/sobrescritas do payload.
     */
    public function alterar(
        array $documento,
        string $urlEnvio,
        int $tipoAviso,
        string $utilizaWebhook = 'S',
        string $versaoLayout = '1',
        array $extras = [],
    ): WebhookCobrancaResposta {
        return $this->cadastrar($this->payload(
            self::TIPO_ALTERACAO, $documento, $urlEnvio, $tipoAviso, $utilizaWebhook, $versaoLayout, $extras,
        ));
    }

    /**
     * Consulta o cadastro de webhook (tipoCadastro = 'C'). A resposta traz a
     * URL cadastrada, se está ativo e quando foi atualizado.
     *
     * @param  array{cpfCnpj: string, filial: string, controle: string}  $documento  CPF/CNPJ decomposto do beneficiário.
     * @param  array<string, mixed>  $extras  Campos adicionais/sobrescritas do payload.
     */
    public function consultar(
        array $documento,
        string $urlEnvio = '',
        int $tipoAviso = 1,
        string $utilizaWebhook = 'S',
        string $versaoLayout = '1',
        array $extras = [],
    ): WebhookCobrancaResposta {
        return $this->cadastrar($this->payload(
            self::TIPO_CONSULTA, $documento, $urlEnvio, $tipoAviso, $utilizaWebhook, $versaoLayout, $extras,
        ));
    }

    /**
     * Exclui o cadastro de webhook (tipoCadastro = 'E').
     *
     * @param  array{cpfCnpj: string, filial: string, controle: string}  $documento  CPF/CNPJ decomposto do beneficiário.
     * @param  array<string, mixed>  $extras  Campos adicionais/sobrescritas do payload.
     */
    public function excluir(
        array $documento,
        string $urlEnvio = '',
        int $tipoAviso = 1,
        string $utilizaWebhook = 'N',
        string $versaoLayout = '1',
        array $extras = [],
    ): WebhookCobrancaResposta {
        return $this->cadastrar($this->payload(
            self::TIPO_EXCLUSAO, $documento, $urlEnvio, $tipoAviso, $utilizaWebhook, $versaoLayout, $extras,
        ));
    }

    /**
     * @param  array{cpfCnpj: string, filial: string, controle: string}  $documento
     * @param  array<string, mixed>  $extras
     * @return array<string, mixed>
     */
    private function payload(
        string $tipoCadastro,
        array $documento,
        string $urlEnvio,
        int $tipoAviso,
        string $utilizaWebhook,
        string $versaoLayout,
        array $extras,
    ): array {
        return array_merge([
            'documento' => $documento,
            'versaoLayout' => $versaoLayout,
            'tipoCadastro' => $tipoCadastro,
            'utilizaWebhook' => $utilizaWebhook,
            'urlEnvio' => $urlEnvio,
            'tipoAviso' => $tipoAviso,
        ], $extras);
    }
}
