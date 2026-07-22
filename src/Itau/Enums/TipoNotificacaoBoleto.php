<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Enums;

/**
 * Eventos que o webhook de boletos do Itaú notifica em tempo real.
 *
 *  - BAIXA_OPERACIONAL: o boleto foi PAGO (baixa operacional).
 *  - BAIXA_EFETIVA:     o valor foi LIQUIDADO/creditado na conta.
 *
 * Um cadastro é feito por tipo — assinar os dois gera dois registros para o
 * mesmo beneficiário.
 */
enum TipoNotificacaoBoleto: string
{
    case BAIXA_OPERACIONAL = 'BAIXA_OPERACIONAL';
    case BAIXA_EFETIVA = 'BAIXA_EFETIVA';
}
