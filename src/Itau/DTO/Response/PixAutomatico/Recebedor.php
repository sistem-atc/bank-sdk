<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `recebedor` do Pix Automático (usuário recebedor). Na recorrência vem
 * `cnpj`/`nome`/`convenio`; na cobrança recorrente vem os dados da conta de
 * liquidação (`agencia`/`conta`/`tipoConta`).
 */
final class Recebedor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cnpj = null,
        public readonly ?string $nome = null,
        public readonly ?string $convenio = null,
        public readonly ?string $ispbParticipante = null,
        public readonly ?string $agencia = null,
        public readonly ?string $conta = null,
        public readonly ?string $tipoConta = null,
    ) {}
}
