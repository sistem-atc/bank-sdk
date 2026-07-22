<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Dados do RECEBEDOR devolvidos nas respostas de transferência/consulta.
 *
 * Schema `RecebedorResponse` das specs "Pix Transferência - Transferir/Consultar".
 */
final class RecebedorResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** CPF ou CNPJ do usuário recebedor (11 ou 14 posições). */
        public readonly ?string $cpfCnpj = null,
        /** Tipo da chave Pix do recebedor: EMAIL, TELEFONE, CPF-CNPJ, AGENCIACONTA ou EVP. */
        public readonly ?string $tipoChave = null,
        /** CONTA_CORRENTE | CONTA_SALARIO | CONTA_POUPANCA | CONTA_PAGAMENTO. Exibido quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $tipoConta = null,
        /** Chave Pix do recebedor. Obrigatória quando o tipoChave for EMAIL, TELEFONE, CPFCNPJ ou EVP. */
        public readonly ?string $chavePix = null,
        /** ISPB do banco recebedor (8 posições). Exibido quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $ispb = null,
        /** Agência do recebedor. Exibida quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $agencia = null,
        /** Conta do recebedor. Exibida quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $conta = null,
        /** Código do banco recebedor. Exibido quando o tipoChave for AGENCIACONTA. */
        public readonly ?string $banco = null,
        /** Nome do favorecido. */
        public readonly ?string $nomeFavorecido = null,
    ) {}
}
