<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

/**
 * Credenciais da PagBrasil (gateway de pagamento), além das do BankIntegration.
 *
 * A PagBrasil não usa OAuth nas APIs de negócio: cada chamada leva o par
 * `pbtoken` + `secret` — no CORPO (form-urlencoded) na API clássica
 * (/api/order/*, /api/checkout/*, /api/pix/*, /api/payout/) e em HEADERS na
 * API REST do PagStream (/api/v2/pagstream/*).
 *
 * São três segredos distintos, todos definidos no Dashboard (menu Account):
 *
 *   - pbtoken: identificador da conta do lojista (Account > Settings).
 *   - secret phrase: frase secreta que viaja em toda requisição e volta em
 *     todo IPN/webhook — é com ela que se confere a origem da notificação.
 *   - signature key: chave do HMAC-MD5 que assina as respostas XML e os
 *     IPNs/webhooks (Account > Settings). NÃO é a secret phrase.
 *
 * O client_id/client_secret do BankIntegration ficam para o único fluxo OAuth
 * da PagBrasil: o token de curta duração que o backend gera para o
 * PagBrasil.JS (POST /api/oauth/token, Basic auth).
 */
interface PagBrasilIntegration extends BankIntegration
{
    /** Token da conta do lojista (Dashboard, Account > Settings). */
    public function getPbToken(): string;

    /** Secret phrase definida no Dashboard (menu Account). */
    public function getSecretPhrase(): string;

    /**
     * Chave do HMAC-MD5 das respostas e notificações. null = o host não
     * cadastrou a chave, e o SDK não confere assinatura.
     */
    public function getSignatureKey(): ?string;
}
