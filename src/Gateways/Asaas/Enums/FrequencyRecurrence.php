<?php

namespace SistemAtc\Banks\Gateways\Asaas\Enums;

enum FrequencyRecurrence: string
{
    case WEEKLY = 'WEEKLY';
    case MONTHLY = 'MONTHLY';

    public function getLabel(): string
    {
        return match ($this) {
            self::WEEKLY => 'Semanal',
            self::MONTHLY => 'Mensal',
        };
    }
}