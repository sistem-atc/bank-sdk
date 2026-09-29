<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** `payment_method` da API clássica e do relatório de liquidação. */
enum PaymentMethod: string
{
    case CreditCard = 'C';
    case DebitCard = 'D';
    case Boleto = 'B';
    case BoletoFlash = 'F';
    case Pix = 'X';
    /** Só no relatório de liquidação. */
    case Payout = 'T';
    /** Só no relatório de liquidação. */
    case Nupay = 'N';
    /** Débito/crédito lançado pela própria PagBrasil (relatório de liquidação). */
    case PagBrasilAdjustment = 'I';

    public function label(): string
    {
        return match ($this) {
            self::CreditCard => 'Cartão de crédito',
            self::DebitCard => 'Débito Flash',
            self::Boleto => 'Boleto bancário',
            self::BoletoFlash => 'Boleto Flash',
            self::Pix => 'Pix',
            self::Payout => 'Payout',
            self::Nupay => 'Nupay',
            self::PagBrasilAdjustment => 'Débito/crédito PagBrasil',
        };
    }
}
