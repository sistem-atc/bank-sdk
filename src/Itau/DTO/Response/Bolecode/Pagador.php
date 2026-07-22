<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `pagador` do Bolecode Pix (quem paga o boleto). No body de SAÍDA a API
 * acrescenta os indicadores `pagador_eletronico_DDA` (cadastro no DDA) e
 * `praca_protesto` (se o CEP é praça protestável).
 *
 * `pagador_eletronico_DDA` tem sigla em maiúscula colada → #[JsonKey] explícito.
 */
final class Pagador implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?Pessoa $pessoa = null,
        public readonly ?Endereco $endereco = null,
        #[JsonKey('pagador_eletronico_DDA')]
        public readonly ?bool $pagadorEletronicoDDA = null,
        public readonly ?bool $pracaProtesto = null,
    ) {}
}
