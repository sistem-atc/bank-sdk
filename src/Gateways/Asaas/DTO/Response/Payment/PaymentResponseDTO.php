<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment;

use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Discount;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Fine;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Interest;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPayment;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\BillingTypeList;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Split;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\CreditCard;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PaymentEscrow;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PaymentRefunds;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PaymentChargeback;

final class PaymentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $id = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $customer = null,
        public readonly ?string $subscription = null,
        public readonly ?string $installment = null,
        public readonly ?string $checkoutSession = null,
        public readonly ?string $paymentLink = null,
        public readonly ?float $value = null,
        public readonly ?float $netValue = null,
        public readonly ?float $originalValue = null,
        public readonly ?float $interestValue = null,
        public readonly ?string $description = null,
        public readonly ?BillingTypeList $billingType = null,
        public readonly ?CreditCard $creditCard = null,
        public readonly ?bool $canBePaidAfterDueDate = null,
        public readonly ?string $pixTransaction = null,
        public readonly ?string $pixQrCodeId = null,
        public readonly ?StatusPayment $status = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $originalDueDate = null,
        public readonly ?string $paymentDate = null,
        public readonly ?string $clientPaymentDate = null,
        public readonly ?int $installmentNumber = null,
        public readonly ?string $invoiceUrl = null,
        public readonly ?string $invoiceNumber = null,
        public readonly ?string $externalReference = null,
        public readonly ?bool $deleted = null,
        public readonly ?bool $anticipated = null,
        public readonly ?bool $anticipable = null,
        public readonly ?string $creditDate = null,
        public readonly ?string $estimatedCreditDate = null,
        public readonly ?string $transactionReceiptUrl = null,
        public readonly ?string $nossoNumero = null,
        public readonly ?string $bankSlipUrl = null,
        public readonly ?Discount $discount = null,
        public readonly ?Fine $fine = null,
        public readonly ?Interest $interest = null,
        #[ArrayOf(Split::class)] public readonly ?array $split = null,
        public readonly ?bool $postalService = null,
        public readonly ?int $daysAfterDueDateToRegistrationCancellation = null,
        public readonly ?PaymentChargeback $chargeback = null,
        public readonly ?PaymentEscrow $escrow = null,
        #[ArrayOf(PaymentRefunds::class)] public readonly ?array $refunds = null,
    ) {}
}
