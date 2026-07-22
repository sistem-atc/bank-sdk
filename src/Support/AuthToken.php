<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Support;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Token OAuth2 client_credentials devolvido pelo `auth()` de qualquer banco.
 *
 * Não há refresh_token no fluxo client_credentials — quando `expiresAt`
 * passa, reautentica-se com client_id/secret. O `obtainedAt`/`expiresIn`
 * permitem ao host decidir persistência e ao TokenRefresher saber se o token
 * em mãos ainda serve.
 */
final class AuthToken implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly string $accessToken,
        public readonly int $expiresIn = 0,
        public readonly ?string $tokenType = 'Bearer',
        public readonly ?string $scope = null,
        // epoch (s) em que o token foi obtido; base do cálculo de expiração.
        public readonly int $obtainedAt = 0,
    ) {}

    /** Momento (epoch s) em que o token deixa de valer. */
    public function expiresAt(): int
    {
        return $this->obtainedAt + $this->expiresIn;
    }

    /**
     * Já expirou? `safetyMargin` antecipa a expiração pra não usar token na
     * borda (mesmo racional do TOKEN_EXPIRY_SAFETY_MARGIN do Integration).
     */
    public function isExpired(int $now, int $safetyMargin = 60): bool
    {
        if ($this->expiresIn <= 0) {
            return true;
        }

        return $now >= ($this->expiresAt() - $safetyMargin);
    }
}
