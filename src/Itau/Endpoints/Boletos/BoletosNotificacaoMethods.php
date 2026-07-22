<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Boletos;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\NotificacaoBoleto;
use SistemAtc\Banks\Itau\DTO\Response\Boletos\NotificacaoBoletoList;
use SistemAtc\Banks\Itau\Enums\TipoNotificacaoBoleto;

/**
 * Webhook de Boletos (API Boletos v3, host boletos.cloud.itau.com.br) —
 * notificação EM TEMPO REAL de baixa operacional (pagamento) e baixa efetiva
 * (liquidação/crédito), por call-back, sem polling.
 *
 * ATENÇÃO — o esquema difere do webhook Pix:
 *   - Pix: `PUT /webhook/{chave}`, por chave Pix, protegido por mTLS, e o Itaú
 *     acrescenta o sufixo `/pix` na URL cadastrada.
 *   - Boletos (aqui): cadastro por BENEFICIÁRIO (agência+conta+DAC) e o Itaú se
 *     AUTENTICA NO SEU SERVIDOR via OAuth2 antes de entregar a notificação —
 *     por isso o cadastro exige `webhook_oauth_url`, `webhook_client_id`,
 *     `webhook_client_secret` e `webhook_oauth_scope`. Ou seja, o host precisa
 *     EXPOR um endpoint OAuth2 próprio, não só receber o POST.
 *
 * O webhook é único por client_id; assinar os dois eventos gera um registro
 * por tipo.
 */
final class BoletosNotificacaoMethods extends BaseMethods
{
    private const BASE = '/boletos/v3/notificacoes_boletos';

    /**
     * Cadastra a URL de call-back, credenciais e preferências.
     *
     * @param  array{id_beneficiario: string, webhook_url: string, webhook_client_id: string, webhook_client_secret: string, webhook_oauth_url: string, webhook_oauth_scope: string, valor_minimo?: float, tipos_notificacoes: list<string>}  $dados
     */
    public function cadastrar(array $dados): NotificacaoBoleto
    {
        $data = $this->makeRequest(HttpMethod::POST, self::BASE, body: $dados);

        return NotificacaoBoleto::fromArray($data['data'] ?? $data);
    }

    /**
     * Consulta os cadastros de um beneficiário (opcionalmente por tipo).
     * `idBeneficiario` = agência(4) + conta(7) + DAC(1).
     */
    public function consultar(
        string $idBeneficiario,
        ?TipoNotificacaoBoleto $tipo = null,
    ): NotificacaoBoletoList {
        $query = ['id_beneficiario' => $idBeneficiario];

        if ($tipo !== null) {
            $query['tipo_notificacao'] = $tipo->value;
        }

        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $query);

        // A API devolve ora `{data: [...]}`, ora a lista crua.
        return NotificacaoBoletoList::fromArray(
            isset($data['data']) ? $data : ['data' => $data]
        );
    }

    /**
     * Altera URL/credenciais/valor mínimo de um cadastro existente.
     *
     * @param  array{webhook_url?: string, webhook_client_id?: string, webhook_client_secret?: string, webhook_oauth_url?: string, webhook_oauth_scope?: string, valor_minimo?: float}  $dados
     */
    public function alterar(string $idNotificacaoBoleto, array $dados): NotificacaoBoleto
    {
        $data = $this->makeRequest(
            HttpMethod::PATCH,
            self::BASE.'/'.rawurlencode($idNotificacaoBoleto),
            body: $dados,
        );

        return NotificacaoBoleto::fromArray($data['data'] ?? $data);
    }

    /** Cancela o cadastro de notificação (deixa de receber o evento). */
    public function excluir(string $idNotificacaoBoleto): void
    {
        $this->makeRequest(HttpMethod::DELETE, self::BASE.'/'.rawurlencode($idNotificacaoBoleto));
    }
}
