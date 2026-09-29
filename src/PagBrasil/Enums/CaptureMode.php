<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** `cc_auth` do cartão de crédito. */
enum CaptureMode: string
{
    /** Autoriza e captura na hora. */
    case AuthorizeAndCapture = '0';
    /** Só autoriza (pré-autorização, segura o valor por 14 dias). */
    case AuthorizeOnly = '1';
    /** Captura uma pré-autorização (valor ≤ o autorizado, até 14 dias). */
    case Capture = '2';
}
