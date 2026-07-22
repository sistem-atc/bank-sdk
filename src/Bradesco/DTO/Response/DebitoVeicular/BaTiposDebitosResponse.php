<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Tipos de débito (códigos de pagamento) aceitos pelo DETRAN-BA.
 *
 * Origem: POST /v1/debitos-veiculares-ba/detran/listaTiposDebitos
 */
final class BaTiposDebitosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(BaTipoDebitoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
    ) {}
}
