<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau;

use BadMethodCallException;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Itau\Endpoints\Bolecode\BolecodeMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\Boletos;
use SistemAtc\Banks\Itau\Endpoints\Payments\PaymentsMethods;
use SistemAtc\Banks\Itau\Endpoints\Pix\PixMethods;
use SistemAtc\Banks\Itau\Endpoints\PixAutomatico\PixAutomatico;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\RecebimentosPix;
use SistemAtc\Banks\Itau\Endpoints\SaqueTroco\SaqueTroco;
use SistemAtc\Banks\Itau\Endpoints\Statement\StatementMethods;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Itau\Support\ItauHosts;
use SistemAtc\Banks\Itau\Support\OAuth;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Connector Itaú. Cada método de domínio monta um cliente HTTP autenticado
 * (token válido + mTLS + headers do gateway) no HOST do produto e devolve o
 * grupo de métodos. Resolvido pela fachada `Bank::Itau`.
 *
 * Domínios comuns (do BankConnector): auth, statement, pix, payments.
 * Produtos específicos do Itaú (fora da interface cross-bank): boletos,
 * recebimentosPix, pixAutomatico, bolecode, saqueTroco.
 *
 * Cada produto vive num subdomínio próprio — o host é resolvido por ItauHosts.
 */
final class Itau implements BankConnector
{
    public function auth(BankIntegration $integration): AuthToken
    {
        return OAuth::authenticate($integration);
    }

    /**
     * O Itaú não expõe API de DDA (consulta de boletos contra o CNPJ) no
     * conjunto de produtos documentado. Explícito em vez de path chutado.
     */
    public function dda(BankIntegration $integration): never
    {
        throw new BadMethodCallException(
            'Itaú: não há API de DDA no catálogo documentado. '
            .'Para boletos a pagar use os produtos de Cobrança (boletos()) ou o SISPAG (payments()).'
        );
    }

    /** Extrato de conta (Account Statement) — conciliação bancária. */
    public function statement(BankIntegration $integration): StatementMethods
    {
        return new StatementMethods($this->client($integration, 'account_statement'), $integration);
    }

    /** Pix de SAÍDA via SISPAG (Cash Management). */
    public function pix(BankIntegration $integration): PixMethods
    {
        return new PixMethods($this->client($integration, 'default'), $integration);
    }

    /** Consulta de pagamentos SISPAG (todas as modalidades). */
    public function payments(BankIntegration $integration): PaymentsMethods
    {
        return new PaymentsMethods($this->client($integration, 'default'), $integration);
    }

    /** Cobrança por boleto: emissão, instrução, consulta e extrato. */
    public function boletos(BankIntegration $integration): Boletos
    {
        return new Boletos($integration);
    }

    /** Recebimentos Pix — arranjo regulatório Bacen (cob/cobv/pix/loc/webhook). */
    public function recebimentosPix(BankIntegration $integration): RecebimentosPix
    {
        return new RecebimentosPix($integration);
    }

    /** Pix Automático — recorrência, cobrança recorrente e emissão de QR Code. */
    public function pixAutomatico(BankIntegration $integration): PixAutomatico
    {
        return new PixAutomatico($integration);
    }

    /** Bolecode Pix — boleto híbrido com QR Code Pix na mesma emissão. */
    public function bolecode(BankIntegration $integration): BolecodeMethods
    {
        return new BolecodeMethods($this->client($integration, 'pix_recebimentos'), $integration);
    }

    /** Pix Saque e Troco — pontos de atendimento e remuneração. */
    public function saqueTroco(BankIntegration $integration): SaqueTroco
    {
        return new SaqueTroco($integration);
    }

    private function client(BankIntegration $integration, string $product): \Illuminate\Http\Client\PendingRequest
    {
        return HttpClientFactory::make($integration, ItauHosts::resolve($product, $integration));
    }
}
