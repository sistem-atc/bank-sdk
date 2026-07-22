<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `endereco` do pagador / sacador avalista no Bolecode Pix.
 *
 * `sigla_UF` e `numero_CEP` têm sigla em maiúscula colada, que a conversão
 * automática camelCase→snake_case não reconstitui — daí o #[JsonKey] explícito.
 */
final class Endereco implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeLogradouro = null,
        public readonly ?string $nomeBairro = null,
        public readonly ?string $nomeCidade = null,
        #[JsonKey('sigla_UF')]
        public readonly ?string $siglaUF = null,
        #[JsonKey('numero_CEP')]
        public readonly ?string $numeroCEP = null,
    ) {}
}
