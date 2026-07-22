<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Dados do usuário RECEBEDOR (conta creditada).
 *
 * Schema `Recebedor` da spec "Pix Transferência - Consultar"
 * (GET /v1/spi/consulta/transferencia/...).
 */
final class Recebedor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** CPF ou CNPJ do usuário recebedor (11 ou 14 posições). */
        public readonly ?string $cpfCnpj = null,
        /** Chave Pix do recebedor. Exibida quando o tipoChave for EMAIL, TELEFONE, CPFCNPJ ou EVP. */
        public readonly ?string $chavePix = null,
        /** ISPB do banco recebedor (8 posições). Exibido quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $ispb = null,
        /** Agência do banco recebedor. Exibida quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $agencia = null,
        /** Conta do banco recebedor. Exibida quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $conta = null,
        /** CONTA_CORRENTE | CONTA_SALARIO | CONTA_POUPANCA | CONTA_PAGAMENTO. Exibido quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $tipoConta = null,
        /** Nome do favorecido. */
        public readonly ?string $nomeFavorecido = null,
    ) {}
}
