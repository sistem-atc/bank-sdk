<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

use RuntimeException;

/** O host ainda não entregou ao SDK o que o banco precisa pra conferir o aviso (a rota responde 503). */
final class WebhookReceiverNotConfigured extends RuntimeException {}
