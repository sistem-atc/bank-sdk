<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Forma de cobrança. `cardToken` e `pixConsentId` são CREDENCIAIS de
 * cobrança: nunca exibir, logar nem repassar. Na listagem de assinaturas
 * `installments` vem sempre null (consulte a assinatura individual).
 */
final class Payment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $installments = null,
        public readonly ?string $cardToken = null,
        public readonly ?string $pixConsentId = null,
    ) {}
}
