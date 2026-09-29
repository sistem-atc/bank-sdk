<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Endereço do cliente — vira address_* na API clássica. CEP só com dígitos;
 * UF na sigla oficial. `number`, `complement` e `neighborhood` só existem
 * no Link de Pagamento (nos pedidos, o número vai junto em `street`).
 */
final class Address implements RequestPayload
{
    use SerializesRequest;

    public readonly string $zip;

    public readonly string $state;

    public function __construct(
        #[JsonKey('address_street')]
        public readonly string $street,
        #[JsonKey('address_zip')]
        string $zip,
        #[JsonKey('address_city')]
        public readonly string $city,
        #[JsonKey('address_state')]
        string $state,
        #[JsonKey('address_number')]
        public readonly ?string $number = null,
        #[JsonKey('address_number_complement')]
        public readonly ?string $complement = null,
        #[JsonKey('address_neighborhood')]
        public readonly ?string $neighborhood = null,
    ) {
        $this->zip = (string) preg_replace('/\D/', '', $zip);
        $this->state = strtoupper(trim($state));

        if (strlen($this->zip) !== 8) {
            throw new InvalidArgumentException("PagBrasil: CEP inválido '{$zip}'.");
        }

        if (! preg_match('/^[A-Z]{2}$/', $this->state)) {
            throw new InvalidArgumentException("PagBrasil: UF inválida '{$state}'.");
        }
    }
}
