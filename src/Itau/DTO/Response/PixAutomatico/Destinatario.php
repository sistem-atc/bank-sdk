<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `destinatario` da solicitação de confirmação de recorrência — a conta
 * do pagador para a qual a solicitação de aceite é enviada.
 */
final class Destinatario implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $agencia = null,
        public readonly ?string $conta = null,
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $ispbParticipante = null,
    ) {}
}
