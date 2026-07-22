<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Endereço (bloco `endereco`) reutilizado por beneficiário, pagador e sacador
 * avalista nas respostas das APIs de Boletos Cobrança (cash_management/v2 e
 * boletoscash/v2). `sigla_UF` e `numero_CEP` vêm com sigla maiúscula colada,
 * fora da regra automática camel<->snake — daí o #[JsonKey].
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
