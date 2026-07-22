<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Estado do cadastro de webhook de Cobrança após a operação (inclusão,
 * alteração, consulta ou exclusão).
 * Origem: POST /boleto/cobranca-webhook/v1/cadastrar
 */
final class WebhookCobrancaResposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indicador de ativação do webhook ('S'/'N'). */
        public readonly ?string $utilizaWebhook = null,
        /** URL cadastrada para envio da notificação. */
        public readonly ?string $urlEnvio = null,
        /** Data/hora da criação ou atualização do cadastro. */
        public readonly ?string $datahoraAtualizacao = null,
    ) {}
}
