<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts\Endpoints;

use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * PIX: pagamento e consulta. Interface comum a todos os bancos.
 *
 * Operação que MOVIMENTA dinheiro — exige escopo de crédito no app do banco e
 * certificado mTLS em produção. Toda implementação deve ser idempotente por
 * `identificador` (evita pagamento duplicado em retry).
 */
interface PixEndpoint
{
    /**
     * Inicia um pagamento PIX. `identificador` é a chave de idempotência.
     *
     * @param  array{chave?: string, valor: string, identificador: string, descricao?: string}  $dados
     */
    public function pagar(array $dados): DTOInterface;

    /** Consulta a situação de um pagamento pelo identificador de idempotência. */
    public function consultar(string $identificador): DTOInterface;
}
