<?php

namespace SistemAtc\Banks\Gateways\Asaas\Enums;

enum OriginRecurrence: string
{
    case PIX = 'PIX';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::PIX => 'PIX',
        };
    }
}