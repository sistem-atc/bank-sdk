<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Configuração de webhook de recebimento — resposta de `PUT|GET /webhook/{chave}`.
 * O Itaú notifica a `webhookUrl` acrescida do sufixo `/pix` a cada Pix recebido.
 */
final class Webhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $webhookUrl = null,
        public readonly ?string $chave = null,
        public readonly ?string $criacao = null,
    ) {}
}
