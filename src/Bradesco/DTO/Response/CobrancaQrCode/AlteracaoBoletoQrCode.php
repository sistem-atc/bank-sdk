<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da alteração de um boleto com QR Code (mensagem do mainframe).
 *
 * Endpoint: POST /boleto-hibrido/cobranca-alteracao/v1/alteraBoletoConsulta
 */
final class AlteracaoBoletoQrCode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigo = null, // Código da mensagem de sucesso
        public readonly ?string $mensagem = null, // Mensagem de sucesso
    ) {}
}
