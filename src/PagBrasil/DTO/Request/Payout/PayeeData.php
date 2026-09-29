<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Payout;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Favorecido do Payout (payee_*). No CADASTRO todos os campos são
 * obrigatórios (exceto documentLink); na ATUALIZAÇÃO só o `taxid` — os
 * demais são enviados apenas se preenchidos. Nome e documento não mudam
 * depois do cadastro (remova e cadastre de novo).
 *
 * `accountType`: 1 = corrente, 2 = poupança. `branch` sem dígito; `account`
 * com hífen e dígito ("1234568-0"). `bank` = código COMPE.
 */
final class PayeeData implements RequestPayload
{
    use SerializesRequest;

    public const CHECKING = 1;

    public const SAVINGS = 2;

    public readonly string $taxid;

    public function __construct(
        #[JsonKey('payee_taxid')]
        string $taxid,
        #[JsonKey('payee_name')]
        public readonly ?string $name = null,
        #[JsonKey('payee_bank')]
        public readonly ?string $bank = null,
        #[JsonKey('payee_branch')]
        public readonly ?string $branch = null,
        #[JsonKey('payee_account')]
        public readonly ?string $account = null,
        #[JsonKey('payee_account_type')]
        public readonly ?int $accountType = null,
        /** Vira o "product name" dos payouts pra esse favorecido. */
        #[JsonKey('payee_description')]
        public readonly ?string $description = null,
        #[JsonKey('payee_email')]
        public readonly ?string $email = null,
        #[JsonKey('payee_phone')]
        public readonly ?string $phone = null,
        #[JsonKey('payee_street')]
        public readonly ?string $street = null,
        #[JsonKey('payee_zip')]
        public readonly ?string $zip = null,
        #[JsonKey('payee_city')]
        public readonly ?string $city = null,
        #[JsonKey('payee_state')]
        public readonly ?string $state = null,
        /** URL (acessível pela PagBrasil) dos documentos digitalizados. */
        #[JsonKey('payee_document_link')]
        public readonly ?string $documentLink = null,
    ) {
        $this->taxid = Customer::taxId($taxid);
    }

    /** Campos que o cadastro (addpayee) exige e estão faltando. */
    public function missingForCreate(): array
    {
        $required = ['name', 'bank', 'branch', 'account', 'accountType', 'description', 'email', 'phone', 'street', 'zip', 'city', 'state'];

        return array_values(array_filter($required, fn (string $field) => $this->{$field} === null || $this->{$field} === ''));
    }
}
