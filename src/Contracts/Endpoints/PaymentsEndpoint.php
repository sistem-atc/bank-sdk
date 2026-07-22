<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts\Endpoints;

use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Pagamento de contas: boletos (título/tributo/concessionária) e transferências.
 * Interface comum a todos os bancos.
 *
 * Operação que MOVIMENTA dinheiro — mesmas exigências do PIX (escopo de
 * crédito + mTLS + idempotência por `identificador`).
 */
interface PaymentsEndpoint
{
    /**
     * Paga um boleto pela linha digitável / código de barras.
     *
     * @param  array{linha_digitavel?: string, codigo_barras?: string, valor: string, identificador: string, data_pagamento?: string}  $dados
     */
    public function pagarBoleto(array $dados): DTOInterface;

    /** Consulta a situação de um pagamento pelo identificador de idempotência. */
    public function consultar(string $identificador): DTOInterface;
}
