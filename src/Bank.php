<?php

declare(strict_types=1);

namespace SistemAtc\Banks;

use BadMethodCallException;
use SistemAtc\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\AgoraMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Arrecadacao\ArrecadacaoMethods;
use SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Cobranca\Cobranca;
use SistemAtc\Banks\Bradesco\Endpoints\CobrancaQrCode\CobrancaQrCodeMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\PixQrCode;
use SistemAtc\Banks\Bradesco\Endpoints\Ted\TedMethods;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Contracts\Endpoints\DdaEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Itau\Endpoints\Bolecode\BolecodeMethods;
use SistemAtc\Banks\Itau\Endpoints\BoletoNegociado\BoletoNegociadoMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\Boletos;
use SistemAtc\Banks\Itau\Endpoints\Conciliacao\ConciliacaoMethods;
use SistemAtc\Banks\Itau\Endpoints\PixAutomatico\PixAutomatico;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\RecebimentosPix;
use SistemAtc\Banks\Itau\Endpoints\SaqueTroco\SaqueTroco;
use SistemAtc\Banks\Itau\Itau;
use SistemAtc\Banks\PagBrasil\Endpoints\Checkout\CheckoutMethods;
use SistemAtc\Banks\PagBrasil\Endpoints\Orders\OrderMethods;
use SistemAtc\Banks\PagBrasil\Endpoints\PagStream\PagStream as PagBrasilPagStream;
use SistemAtc\Banks\PagBrasil\Endpoints\Payout\PayoutMethods;
use SistemAtc\Banks\PagBrasil\Endpoints\PixAutomatico\PixAutomaticoMethods as PagBrasilPixAutomatico;
use SistemAtc\Banks\PagBrasil\PagBrasil;
use SistemAtc\Banks\PagBrasil\Webhooks\WebhookVerifier;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Entrypoint do SDK bancário. É um enum pra permitir o uso encadeado a partir
 * do case, sem `new`:
 *
 *   Bank::Bradesco->auth($integration);
 *   Bank::Bradesco->cobranca($integration)->titulos()->registrar([...]);
 *   Bank::Itau->statement($integration)->periodo('2026-07-01', '2026-07-31');
 *   Bank::Itau->pix($integration)->pagar([...]);
 *
 * O case resolve pro BankConnector concreto (via `connector()`) e os métodos
 * de fachada delegam. Como cada método é tipado pela INTERFACE de domínio,
 * trocar `Bradesco` por `Itau` não muda o código do consumidor.
 *
 * `Bank::PagBrasil` é um GATEWAY de pagamento, não um banco: mesma fachada,
 * mas sem extrato/DDA/SISPAG — expõe pedidos(), linkPagamento(),
 * pixAutomatico(), payout(), pagStream() e webhook().
 *
 * Sobre `$integration`: banco não tem "sessão de usuário" — a identidade é o
 * app (client_id/secret) + o certificado mTLS da empresa. Esses dados chegam
 * pela implementação de BankIntegration que o host passa em cada chamada,
 * igual ao MarketplaceIntegration do pacote de marketplaces.
 */
enum Bank
{
    case Bradesco;
    case Itau;
    case PagBrasil;

    /** Resolve o connector concreto do banco. */
    public function connector(): BankConnector
    {
        return match ($this) {
            self::Bradesco => new Bradesco(),
            self::Itau => new Itau(),
            self::PagBrasil => new PagBrasil(),
        };
    }

    /**
     * Código de compensação FEBRABAN (útil pro CNAB e conciliação). A
     * PagBrasil é gateway, não banco compensado: sem código, erro explícito.
     */
    public function code(): string
    {
        return match ($this) {
            self::Bradesco => '237',
            self::Itau => '341',
            self::PagBrasil => throw new BadMethodCallException(
                'PagBrasil: gateway de pagamento, não tem código de compensação FEBRABAN.'
            ),
        };
    }

    public function auth(BankIntegration $integration): AuthToken
    {
        return $this->connector()->auth($integration);
    }

    public function dda(BankIntegration $integration): DdaEndpoint
    {
        return $this->connector()->dda($integration);
    }

    public function statement(BankIntegration $integration): StatementEndpoint
    {
        return $this->connector()->statement($integration);
    }

    public function pix(BankIntegration $integration): PixEndpoint
    {
        return $this->connector()->pix($integration);
    }

    public function payments(BankIntegration $integration): PaymentsEndpoint
    {
        return $this->connector()->payments($integration);
    }

    // ── Produtos específicos do Itaú ────────────────────────────────────────
    //
    // Ficam FORA do BankConnector (interface cross-bank) porque cada banco tem
    // um catálogo próprio — forçar o Bradesco a stubar "Bolecode" poluiria o
    // contrato. Acessíveis pela mesma fachada; num banco que não oferece o
    // produto, o erro é explícito em vez de silencioso.

    /** Cobrança por boleto: emissão, instrução, consulta e extrato. */
    public function boletos(BankIntegration $integration): Boletos
    {
        return $this->itau(__FUNCTION__)->boletos($integration);
    }

    /** Recebimentos Pix — arranjo regulatório Bacen (cob/cobv/pix/loc/webhook). */
    public function recebimentosPix(BankIntegration $integration): RecebimentosPix
    {
        return $this->itau(__FUNCTION__)->recebimentosPix($integration);
    }

    /**
     * Pix Automático — recorrência, cobrança recorrente e QR Code. Existe no
     * Itaú (API Bacen) e na PagBrasil (API própria); o tipo de retorno segue
     * o case.
     */
    public function pixAutomatico(BankIntegration $integration): PixAutomatico|PagBrasilPixAutomatico
    {
        if ($this === self::PagBrasil) {
            return $this->pagBrasil(__FUNCTION__)->pixAutomatico($integration);
        }

        return $this->itau(__FUNCTION__)->pixAutomatico($integration);
    }

    /** Bolecode Pix — boleto híbrido com QR Code Pix. */
    public function bolecode(BankIntegration $integration): BolecodeMethods
    {
        return $this->itau(__FUNCTION__)->bolecode($integration);
    }

    /** Pix Saque e Troco — pontos de atendimento e remuneração. */
    public function saqueTroco(BankIntegration $integration): SaqueTroco
    {
        return $this->itau(__FUNCTION__)->saqueTroco($integration);
    }

    /** Conciliação Pix — lançamentos Pix conciliados. */
    public function conciliacao(BankIntegration $integration): ConciliacaoMethods
    {
        return $this->itau(__FUNCTION__)->conciliacao($integration);
    }

    /** Boletos Negociados / Ativos Financeiros — consulta rica, criação e recebíveis. */
    public function boletoNegociado(BankIntegration $integration): BoletoNegociadoMethods
    {
        return $this->itau(__FUNCTION__)->boletoNegociado($integration);
    }

    /** Garante que o case é Itaú antes de delegar um produto exclusivo dele. */
    private function itau(string $domain): Itau
    {
        $connector = $this->connector();

        if (! $connector instanceof Itau) {
            throw new BadMethodCallException(
                "{$this->name}: o domínio '{$domain}' é exclusivo do Itaú e não está disponível neste banco."
            );
        }

        return $connector;
    }

    // ── Produtos específicos do Bradesco ────────────────────────────────────

    /** Cobrança por boleto convencional (registro, baixa, consultas, split, webhook). */
    public function cobranca(BankIntegration $integration): Cobranca
    {
        return $this->bradesco(__FUNCTION__)->cobranca($integration);
    }

    /** Cobrança com QR Code (boleto híbrido / Bolecode). */
    public function cobrancaQrCode(BankIntegration $integration): CobrancaQrCodeMethods
    {
        return $this->bradesco(__FUNCTION__)->cobrancaQrCode($integration);
    }

    /** Recebimentos Pix (Bacen): cobranças, location, Pix recebidos e webhook. */
    public function pixQrCode(BankIntegration $integration): PixQrCode
    {
        return $this->bradesco(__FUNCTION__)->pixQrCode($integration);
    }

    /** Pagamento de contas de consumo e tributos (código de barras). */
    public function arrecadacao(BankIntegration $integration): ArrecadacaoMethods
    {
        return $this->bradesco(__FUNCTION__)->arrecadacao($integration);
    }

    /** TED — transferência interbancária. */
    public function ted(BankIntegration $integration): TedMethods
    {
        return $this->bradesco(__FUNCTION__)->ted($integration);
    }

    /** Débito veicular (IPVA, multas, licenciamento) — SP, MG, PR e BA. */
    public function debitoVeicular(BankIntegration $integration): DebitoVeicularMethods
    {
        return $this->bradesco(__FUNCTION__)->debitoVeicular($integration);
    }

    /** Ágora Investimentos — posições, saldos, extratos e cadastro (somente leitura). */
    public function agora(BankIntegration $integration): AgoraMethods
    {
        return $this->bradesco(__FUNCTION__)->agora($integration);
    }

    /** Garante que o case é Bradesco antes de delegar um produto exclusivo dele. */
    private function bradesco(string $domain): Bradesco
    {
        $connector = $this->connector();

        if (! $connector instanceof Bradesco) {
            throw new BadMethodCallException(
                "{$this->name}: o domínio '{$domain}' é exclusivo do Bradesco e não está disponível neste banco."
            );
        }

        return $connector;
    }

    // ── Produtos da PagBrasil (gateway de pagamento) ────────────────────────

    /** Pedidos: cartão, Débito Flash, boleto e Pix — criar, consultar, estornar, cancelar. */
    public function pedidos(BankIntegration $integration): OrderMethods
    {
        return $this->pagBrasil(__FUNCTION__)->pedidos($integration);
    }

    /** Link de Pagamento (checkout hospedado pela PagBrasil). */
    public function linkPagamento(BankIntegration $integration): CheckoutMethods
    {
        return $this->pagBrasil(__FUNCTION__)->linkPagamento($integration);
    }

    /** Payout — favorecidos e envio de valores. */
    public function payout(BankIntegration $integration): PayoutMethods
    {
        return $this->pagBrasil(__FUNCTION__)->payout($integration);
    }

    /** PagStream — assinaturas recorrentes. */
    public function pagStream(BankIntegration $integration): PagBrasilPagStream
    {
        return $this->pagBrasil(__FUNCTION__)->pagStream($integration);
    }

    /** Autentica e interpreta IPNs/webhooks recebidos. */
    public function webhook(BankIntegration $integration): WebhookVerifier
    {
        return $this->pagBrasil(__FUNCTION__)->webhook($integration);
    }

    /** Garante que o case é PagBrasil antes de delegar um produto exclusivo dela. */
    private function pagBrasil(string $domain): PagBrasil
    {
        $connector = $this->connector();

        if (! $connector instanceof PagBrasil) {
            throw new BadMethodCallException(
                "{$this->name}: o domínio '{$domain}' é exclusivo da PagBrasil e não está disponível neste banco."
            );
        }

        return $connector;
    }
}
