<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `pagador` da recorrência — dados bancários do usuário pagador
 * (retornado em GET /rec/{idRec} e GET /rec/{idRec}/dados-pagador).
 */
final class Pagador implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $codMun = null,
        public readonly ?string $ispbParticipante = null,
        public readonly ?string $agencia = null,
        public readonly ?string $conta = null,
    ) {}
}
