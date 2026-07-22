<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Payments;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Payments\BoletoPayment;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;

/**
 * Pagamento de boletos/contas Bradesco. MOVIMENTA dinheiro — idempotente por
 * `identificador`. Path/formato a confirmar com a spec real.
 */
final class PaymentsMethods extends BaseMethods implements PaymentsEndpoint
{
    private const PATH = '/pagamentos/v1/boletos';

    /** @param array{linha_digitavel?: string, codigo_barras?: string, valor: string, identificador: string, data_pagamento?: string} $dados */
    public function pagarBoleto(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH, body: $dados);

        return BoletoPayment::fromArray($data);
    }

    public function consultar(string $identificador): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH.'/'.rawurlencode($identificador));

        return BoletoPayment::fromArray($data);
    }
}
