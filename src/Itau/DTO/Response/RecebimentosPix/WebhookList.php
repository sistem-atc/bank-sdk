<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Listagem de webhooks cadastrados — resposta de `GET /webhook`.
 *
 * @property array<int, Webhook> $webhooks
 */
final class WebhookList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Webhook> $webhooks */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(Webhook::class)]
        public readonly array $webhooks = [],
    ) {}
}
