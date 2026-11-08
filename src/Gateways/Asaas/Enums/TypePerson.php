<?php

namespace SistemAtc\Banks\Gateways\Asaas\Enums;

enum TypePerson: string
{
    case JURIDICA = 'JURIDICA';
    case FISICA = 'FISICA';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::JURIDICA => 'Juridica',
            self::FISICA => 'Fisica',
        };
    }
}
