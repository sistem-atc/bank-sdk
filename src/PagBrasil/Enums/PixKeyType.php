<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** `customer_pix_key_type` do estorno via chave Pix. */
enum PixKeyType: string
{
    case TaxId = 'taxid';
    case Phone = 'phone';
    case Email = 'email';
    /** Chave aleatória (EVP). */
    case Alternative = 'alternative';
}
