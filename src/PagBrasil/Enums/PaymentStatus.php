<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/**
 * `payment_status` do IPN (notificação de pagamento/estorno). É um código de
 * UMA letra, diferente do `order_status` de duas letras da consulta.
 */
enum PaymentStatus: string
{
    case Authorized = 'A';
    case Failed = 'F';
    case Rejected = 'R';
    case Chargeback = 'C';
    case RefundProcessed = 'P';
    case RefundRejected = 'J';

    public function label(): string
    {
        return match ($this) {
            self::Authorized => 'Autorizado',
            self::Failed => 'Falhou',
            self::Rejected => 'Rejeitado',
            self::Chargeback => 'Chargeback',
            self::RefundProcessed => 'Estorno processado',
            self::RefundRejected => 'Estorno rejeitado',
        };
    }
}
