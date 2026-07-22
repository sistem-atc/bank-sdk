<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Dados bancários do usuário PAGADOR (conta debitada).
 *
 * Schema `Pagador` das specs "Pix Transferência - Consultar/Transferir"
 * (GET /v1/spi/consulta/transferencia/...).
 */
final class Pagador implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** CPF ou CNPJ do usuário pagador (11 ou 14 posições). */
        public readonly ?string $cpfCnpj = null,
        /** Agência do usuário pagador (4 posições). */
        public readonly ?string $agencia = null,
        /** Conta do usuário pagador. */
        public readonly ?string $conta = null,
        /** CONTA_CORRENTE | CONTA_SALARIO | CONTA_POUPANCA | CONTA_PAGAMENTO. */
        public readonly ?string $tipoConta = null,
    ) {}
}
