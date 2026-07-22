<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `pagador` das respostas de Boletos Cobrança. `pagador_eletronico_DDA`
 * vem com sigla maiúscula colada, fora da regra automática — daí o #[JsonKey].
 */
final class Pagador implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?Pessoa $pessoa = null,
        public readonly ?Endereco $endereco = null,
        public readonly ?string $textoEnderecoEmail = null,
        public readonly ?string $email = null,
        #[JsonKey('pagador_eletronico_DDA')]
        public readonly ?bool $pagadorEletronicoDDA = null,
        public readonly ?bool $pracaProtesto = null,
    ) {}
}
