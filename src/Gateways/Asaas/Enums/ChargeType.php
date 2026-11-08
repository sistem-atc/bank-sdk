<?php

namespace SistemAtc\Banks\Gateways\Asaas\Enums;

enum ChargeType: string
{
    case DETACHED = 'DETACHED';
    case RECURRENT = 'RECURRENT';
    case INSTALLMENT = 'INSTALLMENT';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DETACHED => 'Avulsa',
            self::RECURRENT => 'Recorrente',
            self::INSTALLMENT => 'Parcelada',
        };
    }
}
