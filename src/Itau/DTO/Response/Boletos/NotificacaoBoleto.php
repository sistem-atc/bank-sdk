<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Enums\TipoNotificacaoBoleto;

/**
 * Um cadastro de webhook de boletos (API Boletos v3 — `notificacoes_boletos`).
 *
 * Note que a RESPOSTA traz `tipo_notificacao` no singular (um registro por
 * evento), enquanto o CADASTRO envia `tipos_notificacoes` (array) — o Itaú
 * desdobra a assinatura em um registro por tipo.
 *
 * O `webhook_client_secret` NUNCA volta nas consultas (só é enviado no
 * cadastro/alteração), por isso não faz parte deste DTO.
 */
final class NotificacaoBoleto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idNotificacaoBoleto = null,
        // Agência (4) + Conta (7) + DAC (1).
        public readonly ?string $idBeneficiario = null,
        public readonly ?string $webhookUrl = null,
        // Endpoint OAuth2 DO CLIENTE: o Itaú se autentica nele antes de notificar.
        public readonly ?string $webhookOauthUrl = null,
        public readonly ?string $webhookOauthScope = null,
        // Piso de valor: boletos abaixo disso não geram notificação.
        public readonly ?float $valorMinimo = null,
        public readonly ?string $dataCriacao = null,
        public readonly ?TipoNotificacaoBoleto $tipoNotificacao = null,
    ) {}
}
