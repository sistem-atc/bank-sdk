<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\AutomaticPixTerms;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Só o consentimento Pix Automático, sem pagamento agora (jornada 2) —
 * /api/pix/rec/add. O pagador vai em payer_name/payer_taxid.
 */
final class AutomaticPixConsent implements RequestPayload
{
    use SerializesRequest;

    public readonly string $payerTaxid;

    public function __construct(
        #[JsonKey('payer_name')]
        public readonly string $payerName,
        #[JsonKey('payer_taxid')]
        string $payerTaxid,
        #[Flatten]
        public readonly AutomaticPixTerms $terms,
    ) {
        $this->payerTaxid = Customer::taxId($payerTaxid);
    }
}
