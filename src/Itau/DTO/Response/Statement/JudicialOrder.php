<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Ordem judicial de bloqueio (BacenJud/SISBAJUD) — item de `data[]` de
 * `GET /statements/{statementsId}/judicial-orders`. A API já entrega as chaves
 * em camelCase. `personType` ∈ {F, J}; `blockType` ∈ {VALOR, ...};
 * `blockStatus` ∈ {CUMPRIDA, ...}. Valores de bloqueio vêm numéricos.
 */
final class JudicialOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $blockOrderId = null,
        public readonly ?int $originCode = null,
        public readonly ?string $protocolNumber = null,
        public readonly ?int $sequenceCode = null,
        public readonly ?int $reiterationCode = null,
        public readonly ?string $documentNumber = null,
        public readonly ?string $personType = null,
        public readonly ?string $defendantName = null,
        public readonly ?string $blockDate = null,
        public readonly ?string $officialNumber = null,
        public readonly ?string $processNumber = null,
        public readonly ?string $actionType = null,
        public readonly ?int $courtCode = null,
        public readonly ?string $courtName = null,
        public readonly ?string $courtState = null,
        public readonly ?string $courtPhoneNumber = null,
        public readonly ?string $districtName = null,
        public readonly ?string $judgeName = null,
        public readonly ?string $tribunalName = null,
        public readonly ?string $requestingParty = null,
        public readonly ?string $blockType = null,
        public readonly ?string $blockStatus = null,
        public readonly ?float $blockOrderValue = null,
        public readonly ?float $blockEffectiveValue = null,
        public readonly ?string $blockObservation = null,
    ) {}
}
