<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/** Autenticação 3DS obtida no PagBrasil.JS (getAuth3DS / doAuthenticate) — auth3ds_*. */
final class ThreeDSecure implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[JsonKey('auth3ds_type')]
        public readonly string $type,
        #[JsonKey('auth3ds_cavv')]
        public readonly string $cavv,
        #[JsonKey('auth3ds_version')]
        public readonly string $version,
        #[JsonKey('auth3ds_eci')]
        public readonly string $eci,
        #[JsonKey('auth3ds_reference_id')]
        public readonly string $referenceId,
        #[JsonKey('auth3ds_xid')]
        public readonly string $xid,
    ) {}
}
