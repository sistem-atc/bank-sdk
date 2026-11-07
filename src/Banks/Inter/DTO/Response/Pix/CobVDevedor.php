<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Os campos aninhados sob o objeto devedor são opcionais e identificam a
 * pessoa ou a instituição a quem a cobrança está endereçada. Não identifica,
 * necessariamente, quem irá efetivamente realizar o pagamento. Não é permitido
 * que o campo devedor.cpf e campo devedor.cnpj estejam preenchidos ao mesmo
 * tempo. Se o campo devedor.nome está preenchido, então deve existir ou um
 * devedor.cpf ou um campo devedor.cnpj preenchido.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobVDevedor`).
 */
final class CobVDevedor implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Nome do usuário. */
        public readonly ?string $nome = null,
        /** CPF do usuário. */
        public readonly ?string $cpf = null,
        /** Email do usuário. */
        public readonly ?string $email = null,
        /** Logradouro do usuário. */
        public readonly ?string $logradouro = null,
        /** Cidade do usuário. */
        public readonly ?string $cidade = null,
        /** UF do usuário. */
        public readonly ?string $uf = null,
        /** CEP do usuário. */
        public readonly ?string $cep = null,
        /** CNPJ do usuário. */
        public readonly ?string $cnpj = null,
    ) {}
}
