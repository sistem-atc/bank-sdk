<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** `cc_brand` da consulta de pedido de cartão. */
enum CardBrand: string
{
    case Mastercard = 'M';
    case Visa = 'V';
    case Diners = 'D';
    case Amex = 'A';
    case Elo = 'E';
}
