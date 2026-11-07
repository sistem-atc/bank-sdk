<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Pagador` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Pagador`).
 */
final class Pagador implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** CPF/CNPJ do pagador do título */
        public readonly ?string $cpfCnpj = null,
        /** Tipo do pagador: * `FISICA` - Pessoa Física * `JURIDICA` - Pessoa Jurídica */
        public readonly ?string $tipoPessoa = null,
        /** Nome do pagador */
        public readonly ?string $nome = null,
        /** Endereço do pagador */
        public readonly ?string $endereco = null,
        /** Bairro do pagador */
        public readonly ?string $bairro = null,
        /** Cidade do pagador */
        public readonly ?string $cidade = null,
        /** UF */
        public readonly ?string $uf = null,
        /** CEP do pagador */
        public readonly ?string $cep = null,
        /** E-mail do pagador */
        public readonly ?string $email = null,
        /** DDD do telefone do pagador */
        public readonly ?string $ddd = null,
        /** Telefone do pagador */
        public readonly ?string $telefone = null,
        /** Número no logradouro do pagador */
        public readonly ?string $numero = null,
        /** Complemento do endereço do pagador */
        public readonly ?string $complemento = null,
    ) {}
}
