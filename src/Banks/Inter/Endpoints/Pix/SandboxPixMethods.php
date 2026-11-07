<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use LogicException;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\MakePaymentCobCobvResponse;
use SistemAtc\Banks\Support\Environment;
use Illuminate\Http\Client\PendingRequest;

/**
 * Utilitários que SÓ existem no sandbox do Inter. Recusa rodar contra
 * produção antes de qualquer chamada.
 */
final class SandboxPixMethods extends BaseMethods
{
    public function __construct(PendingRequest $httpClient, BankIntegration $integration, string $basePath)
    {
        if (Environment::forIntegration($integration) !== Environment::SANDBOX) {
            throw new LogicException('Inter: utilitários de sandbox não rodam em produção.');
        }

        parent::__construct($httpClient, $integration, $basePath);
    }

    /**
     * `POST /sandbox/cob/pagamento` — simula o pagamento de uma COB/COBV pelo
     * Pix Copia e Cola, pra testar o webhook.
     *
     * @param  array<string, mixed>  $dados  corpo MakePaymentCobCobv
     */
    public function simularPagamento(array $dados): MakePaymentCobCobvResponse
    {
        return MakePaymentCobCobvResponse::fromArray($this->makeRequest(HttpMethod::POST, '/sandbox/cob/pagamento', body: $dados));
    }
}
