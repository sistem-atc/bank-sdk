<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Contracts\DTOInterface;

/** Marca as notificações (IPN/webhook) da PagBrasil devolvidas pelo WebhookVerifier. */
interface Notification extends DTOInterface
{
}
