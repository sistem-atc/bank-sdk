<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco;

use BadMethodCallException;
use SistemAtc\Banks\Bradesco\Endpoints\Agora\AgoraMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Arrecadacao\ArrecadacaoMethods;
use SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Cobranca\Cobranca;
use SistemAtc\Banks\Bradesco\Endpoints\CobrancaQrCode\CobrancaQrCodeMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PagamentoBoletos\PagamentoBoletosMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\PixQrCode;
use SistemAtc\Banks\Bradesco\Endpoints\PixTransferencias\PixTransferenciasMethods;
use SistemAtc\Banks\Bradesco\Endpoints\SaldoExtrato\SaldoExtratoMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Ted\TedMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Bradesco\Support\OAuth;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Connector Bradesco. Cada método monta um cliente HTTP autenticado no host e
 * autorizador da FAMÍLIA do produto (open_api ou pix) e devolve o grupo de
 * métodos. Resolvido pela fachada `Bank::Bradesco`.
 *
 * Domínios comuns (BankConnector): auth, statement, pix, payments — apontam
 * para as implementações reais, o que mantém Bradesco e Itaú intercambiáveis.
 * Produtos específicos do Bradesco: cobranca, cobrancaQrCode, pixQrCode,
 * arrecadacao, ted.
 */
final class Bradesco implements BankConnector
{
    public function auth(BankIntegration $integration): AuthToken
    {
        return OAuth::authenticate($integration);
    }

    /**
     * O catálogo do Bradesco não expõe API de DDA (consulta de boletos contra
     * o CNPJ). Explícito em vez de path chutado.
     */
    public function dda(BankIntegration $integration): never
    {
        throw new BadMethodCallException(
            'Bradesco: não há API de DDA no catálogo documentado. '
            .'Para pagar boletos use pagamentoBoletos(); para cobrar, cobranca().'
        );
    }

    /** Extrato/saldo de contas PJ — conciliação bancária. */
    public function statement(BankIntegration $integration): SaldoExtratoMethods
    {
        return new SaldoExtratoMethods($this->client($integration), $integration);
    }

    /** Pix de SAÍDA (SPI) — família PIX. */
    public function pix(BankIntegration $integration): PixTransferenciasMethods
    {
        return new PixTransferenciasMethods(
            $this->client($integration, BradescoHosts::FAMILY_PIX),
            $integration,
        );
    }

    /** Pagamento de boletos de cobrança (valida → pré-efetiva → efetiva). */
    public function payments(BankIntegration $integration): PagamentoBoletosMethods
    {
        return new PagamentoBoletosMethods($this->client($integration), $integration);
    }

    // ── Produtos específicos do Bradesco ────────────────────────────────────

    /** Cobrança por boleto convencional (registro, baixa, consultas, split, webhook). */
    public function cobranca(BankIntegration $integration): Cobranca
    {
        return new Cobranca($integration);
    }

    /** Cobrança com QR Code (boleto híbrido / Bolecode). */
    public function cobrancaQrCode(BankIntegration $integration): CobrancaQrCodeMethods
    {
        return new CobrancaQrCodeMethods($this->client($integration), $integration);
    }

    /** Recebimentos Pix — cobranças, location, Pix recebidos e webhook (Bacen). */
    public function pixQrCode(BankIntegration $integration): PixQrCode
    {
        return new PixQrCode($integration);
    }

    /** Pix - transferências (SPI). Alias explícito do domínio comum `pix()`. */
    public function pixTransferencias(BankIntegration $integration): PixTransferenciasMethods
    {
        return $this->pix($integration);
    }

    /** Saldo e extrato. Alias explícito do domínio comum `statement()`. */
    public function saldoExtrato(BankIntegration $integration): SaldoExtratoMethods
    {
        return $this->statement($integration);
    }

    /** Pagamento de boletos. Alias explícito do domínio comum `payments()`. */
    public function pagamentoBoletos(BankIntegration $integration): PagamentoBoletosMethods
    {
        return $this->payments($integration);
    }

    /** Pagamento de contas de consumo e tributos (código de barras). */
    public function arrecadacao(BankIntegration $integration): ArrecadacaoMethods
    {
        return new ArrecadacaoMethods($this->client($integration), $integration);
    }

    /** TED — transferência interbancária. */
    public function ted(BankIntegration $integration): TedMethods
    {
        return new TedMethods($this->client($integration), $integration);
    }

    /** Débito veicular (IPVA, multas, licenciamento) — SP, MG, PR e BA. */
    public function debitoVeicular(BankIntegration $integration): DebitoVeicularMethods
    {
        return new DebitoVeicularMethods($this->client($integration), $integration);
    }

    /** Ágora Investimentos — posições, saldos, extratos e cadastro (somente leitura). */
    public function agora(BankIntegration $integration): AgoraMethods
    {
        return new AgoraMethods($this->client($integration), $integration);
    }

    private function client(
        BankIntegration $integration,
        string $family = BradescoHosts::FAMILY_OPEN_API,
    ): \Illuminate\Http\Client\PendingRequest {
        return HttpClientFactory::make($integration, $family);
    }
}
