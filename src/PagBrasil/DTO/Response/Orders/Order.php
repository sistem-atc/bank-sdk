<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Orders;

use DateTimeImmutable;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\PagBrasil\Enums\CardBrand;
use SistemAtc\Banks\PagBrasil\Enums\CardErrorCode;
use SistemAtc\Banks\PagBrasil\Enums\OrderStatus;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\PagBrasil\Support\Dates;

/**
 * Pedido da API clássica — resposta de /api/order/add, /get, /cancel, da
 * prorrogação de Pix e da criação de consentimento Pix Automático. É a UNIÃO
 * dos campos de todos os meios de pagamento: cada meio preenche o seu
 * subconjunto e o resto fica null.
 *
 *   - cartão/débito: authorization_code, nsu, cc_*, soft_descriptor, error_code
 *   - boleto: url_boleto
 *   - Pix: expiration_date/_time, pix_image, pix_code (+ pix_rec_id no Pix Automático)
 *   - link de pagamento: url_payment
 *
 * Valores monetários ficam como string ("39.50") pra não perder precisão.
 * Datas vêm em MM/DD/YYYY — use `date('payment_date')`.
 */
final class Order implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $order = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $submissionDate = null,
        public readonly ?string $expirationDate = null,
        public readonly ?string $expirationTime = null,
        public readonly ?string $urlBoleto = null,
        public readonly ?string $urlPayment = null,
        public readonly ?string $companyCode = null,
        public readonly ?string $paymentId = null,
        public readonly ?string $paymentInstructions = null,
        public readonly ?string $pixImage = null,
        public readonly ?string $pixCode = null,
        public readonly ?string $pixRecId = null,
        public readonly ?string $orderStatus = null,
        public readonly ?string $authorizationCode = null,
        public readonly ?string $nsu = null,
        public readonly ?string $paymentDate = null,
        public readonly ?string $productName = null,
        public readonly ?string $customerName = null,
        public readonly ?string $customerTaxid = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $customerPhone = null,
        public readonly ?string $addressStreet = null,
        public readonly ?string $addressZip = null,
        public readonly ?string $addressCity = null,
        public readonly ?string $addressState = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $amountPaid = null,
        public readonly ?string $ccInstallments = null,
        public readonly ?string $ccBrand = null,
        public readonly ?string $ccHolder = null,
        public readonly ?string $ccNumber = null,
        public readonly ?string $ccExpiration = null,
        public readonly ?string $softDescriptor = null,
        public readonly ?string $amountRefunded = null,
        public readonly ?string $refundDate = null,
        public readonly ?string $refundInfo = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $signature = null,
    ) {}

    public function status(): ?OrderStatus
    {
        return $this->orderStatus === null ? null : OrderStatus::tryFrom($this->orderStatus);
    }

    public function method(): ?PaymentMethod
    {
        return $this->paymentMethod === null ? null : PaymentMethod::tryFrom($this->paymentMethod);
    }

    public function brand(): ?CardBrand
    {
        return $this->ccBrand === null ? null : CardBrand::tryFrom($this->ccBrand);
    }

    /** Motivo da falha de cartão (status PF/PR). */
    public function cardError(): ?CardErrorCode
    {
        return $this->errorCode === null ? null : CardErrorCode::tryFrom($this->errorCode);
    }

    public function isPaid(): bool
    {
        return $this->status() === OrderStatus::Completed;
    }

    /**
     * Data de um dos campos de data (MM/DD/YYYY → objeto).
     *
     * @param  'submission_date'|'expiration_date'|'payment_date'|'refund_date'  $field
     */
    public function date(string $field): ?DateTimeImmutable
    {
        return Dates::parse(match ($field) {
            'submission_date' => $this->submissionDate,
            'expiration_date' => $this->expirationDate,
            'payment_date' => $this->paymentDate,
            'refund_date' => $this->refundDate,
            default => null,
        });
    }
}
