<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Pessoa (cedente/beneficiário, sacado/pagador ou sacador/avalista) devolvida
 * pela consulta de título específico.
 * Origem: POST /boleto/cobranca-consulta/v1/consultar
 */
final class Pessoa implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** CNPJ/CPF da pessoa. */
        public readonly ?string $cnpj = null,
        /** Nome/razão social. */
        public readonly ?string $nome = null,
        /** Logradouro. */
        public readonly ?string $endereco = null,
        /** Número do logradouro. */
        public readonly ?string $numero = null,
        /** Complemento do logradouro. */
        public readonly ?string $complemento = null,
        /** Bairro. */
        public readonly ?string $bairro = null,
        /** CEP (raiz). */
        public readonly ?string $cep = null,
        /** Complemento do CEP (sufixo). */
        public readonly ?string $cepc = null,
        /** Município. */
        public readonly ?string $cidade = null,
        /** Unidade federativa. */
        public readonly ?string $uf = null,
    ) {}
}
