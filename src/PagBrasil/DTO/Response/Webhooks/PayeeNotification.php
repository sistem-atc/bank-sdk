<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\PagBrasil\DTO\Response\Payout\Payee;

/**
 * Webhook de mudança de status de favorecido do Payout (action addpayee,
 * updatepayee ou deletepayee). Os campos do favorecido vêm soltos no payload
 * e são reunidos em `payee`.
 */
final class PayeeNotification implements Notification
{
    public function __construct(
        public readonly ?string $action = null,
        public readonly ?Payee $payee = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        return new static(
            isset($data['action']) ? (string) $data['action'] : null,
            Payee::fromArray($data),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return ['action' => $this->action] + ($this->payee?->toArray() ?? []);
    }
}
