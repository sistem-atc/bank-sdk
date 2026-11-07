<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `DadosConta` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `DadosConta`).
 */
final class DadosConta implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** O atributo em questão só é retornado em caso de Pagamento Pix com dados bancários. */
        public readonly ?string $codIspb = null,
        /** O atributo em questão só é retornado em caso de Pagamento Pix com dados bancários. */
        public readonly ?string $codAgencia = null,
        /** O atributo em questão só é retornado em caso de Pagamento Pix com dados bancários. */
        public readonly ?string $nroConta = null,
        /** Caso seja um CPF, o dado retornado será mascarado. (ex:\***.777.888-\**) */
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $nome = null,
        /** O atributo em questão só é retornado em caso de Pagamento Pix com dados bancários. */
        public readonly ?string $tipoConta = null,
    ) {}
}
