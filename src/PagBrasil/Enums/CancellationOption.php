<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/**
 * Motivo de cancelamento de assinatura. Nas opções pré-definidas a PagBrasil
 * grava um texto fixo em português; só `Other` usa o texto enviado.
 */
enum CancellationOption: string
{
    case DoesNotWantProduct = 'does_not_want_product';
    case NotSatisfied = 'not_satisfied';
    case Regretted = 'regretted';
    case Other = 'other';
}
