<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Configuração de webhook (recorrência ou cobrança) — resposta de
 * `GET /webhookrec` e `GET /webhookcobr`. Nos endpoints PUT/DELETE o corpo pode
 * vir vazio (200/204); nesse caso o DTO fica com os campos nulos.
 */
final class WebhookConfig implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $webhookUrl = null,
        public readonly ?string $criacao = null,
    ) {}
}
