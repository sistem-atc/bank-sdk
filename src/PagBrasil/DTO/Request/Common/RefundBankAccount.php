<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Conta do CLIENTE pra estorno por transferência (quando o estorno pelo meio
 * original é rejeitado). O titular tem de ser o próprio cliente.
 */
final class RefundBankAccount implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        /** Código COMPE do banco ("001"). */
        #[JsonKey('customer_bank')]
        public readonly string $bank,
        #[JsonKey('customer_branch')]
        public readonly string $branch,
        /** Com hífen e dígito: "12345-6". */
        #[JsonKey('customer_account')]
        public readonly string $account,
    ) {}
}
