<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use DateTimeInterface;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\RecurrenceCycle;

/**
 * Termos do consentimento Pix Automático (pix_rec_*). Usado no pagamento com
 * consentimento, no consentimento avulso e no Link de Pagamento.
 *
 * `firstRecurrence` ≥ 2 dias úteis depois do pagamento/autorização, senão o
 * banco recusa. `retry` = true é o recomendado: sem ele não há retentativa.
 */
final class AutomaticPixTerms implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[JsonKey('pix_rec_cycle')]
        public readonly RecurrenceCycle $cycle,
        #[JsonKey('pix_rec_first_recurrence')]
        public readonly DateTimeInterface|string $firstRecurrence,
        /** Até 19 caracteres — aparece no app do banco do cliente. */
        #[JsonKey('pix_rec_description')]
        public readonly ?string $description = null,
        #[JsonKey('pix_rec_expiration')]
        public readonly DateTimeInterface|string|null $expiration = null,
        #[JsonKey('pix_rec_minimum_amount'), Money]
        public readonly int|float|string|null $minimumAmount = null,
        #[JsonKey('pix_rec_retry')]
        public readonly bool $retry = true,
    ) {}
}
