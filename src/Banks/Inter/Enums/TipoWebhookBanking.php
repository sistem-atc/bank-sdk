<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Enums;

/** Tipos de webhook da API Banking (`/banking/v2/webhooks/{tipoWebhook}`). */
enum TipoWebhookBanking: string
{
    case PixPagamento = 'pix-pagamento';
    case BoletoPagamento = 'boleto-pagamento';
}
