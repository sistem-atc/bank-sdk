<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\PagBrasil\Enums\CardErrorCode;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\PagBrasil\Enums\PaymentStatus;

/**
 * IPN de pagamento ou de estorno/chargeback (cartão, débito, Pix, Pix
 * Automático, estorno de boleto). `paymentStatus` de UMA letra: A, F, R, C,
 * P, J — ver PaymentStatus. `amountRefunded` só vem no IPN de estorno;
 * `ccAuth` só no de cartão.
 */
final class PaymentNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $paymentMethod = null,
        public readonly ?string $order = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $amountRefunded = null,
        public readonly ?string $paymentStatus = null,
        public readonly ?string $authorizationCode = null,
        public readonly ?string $ccAuth = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $signature = null,
    ) {}

    public function status(): ?PaymentStatus
    {
        return $this->paymentStatus === null ? null : PaymentStatus::tryFrom($this->paymentStatus);
    }

    public function method(): ?PaymentMethod
    {
        return $this->paymentMethod === null ? null : PaymentMethod::tryFrom($this->paymentMethod);
    }

    public function cardError(): ?CardErrorCode
    {
        return $this->errorCode === null ? null : CardErrorCode::tryFrom($this->errorCode);
    }

    /** IPN de estorno/chargeback (e não de autorização)? */
    public function isRefund(): bool
    {
        return in_array($this->status(), [PaymentStatus::RefundProcessed, PaymentStatus::RefundRejected, PaymentStatus::Chargeback], true);
    }
}
