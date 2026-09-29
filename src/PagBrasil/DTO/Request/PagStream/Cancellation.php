<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\CancellationOption;

/**
 * Cancelamento de assinatura (irreversível). `reason` só é gravado com
 * `option = Other`. `canceledBy` até 32 caracteres (default "merchant").
 */
final class Cancellation implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[JsonKey('cancellation_option')]
        public readonly ?CancellationOption $option = null,
        #[JsonKey('cancellation_reason')]
        public readonly ?string $reason = null,
        public readonly ?string $canceledBy = null,
    ) {
        if ($canceledBy !== null && mb_strlen($canceledBy) > 32) {
            throw new InvalidArgumentException('PagBrasil: canceledBy tem no máximo 32 caracteres.');
        }
    }
}
