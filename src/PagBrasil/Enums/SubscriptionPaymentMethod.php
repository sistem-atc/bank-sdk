<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** Forma de cobrança de uma assinatura PagStream. */
enum SubscriptionPaymentMethod: string
{
    case CreditCard = 'credit_card';
    case AutomaticPix = 'pix_automatico';
    case PaymentLink = 'payment_link';
}
