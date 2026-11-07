<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Dados criados ou alterados da cobrança imediata via API Pix
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobCompleta`).
 *
 * @property array<int, InfoAdicional> $infoAdicionais
 * @property array<int, Pix> $pix
 */
final class CobCompleta implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Os campos aninhados sob o objeto devedor são opcionais e identificam a pessoa ou a instituição a quem a cobrança está endereçada. Não identifica, nece… */
        public readonly ?Pessoa $devedor = null,
        /** Identificador da localização do payload. */
        public readonly ?PayloadLocationCob $loc = null,
        /** Localização do Payload a ser informada na criação da cobrança. */
        public readonly ?string $location = null,
        public readonly ?string $status = null,
        /** valores monetários referentes à cobrança. */
        public readonly ?CobValor $valor = null,
        public readonly ?CalendarioCobSolicitada $calendario = null,
        public readonly ?string $txid = null,
        /** # O campo `revisao` Denota a revisão da cobrança. Sempre começa em zero. Sempre varia em acréscimos de 1. O incremento em uma cobrança deve ocorrer se… */
        public readonly ?int $revisao = null,
        /** Este campo retorna o valor do Pix Copia e Cola correspondente à cobrança. Trata-se da sequência de caracteres que representa o BR Code. */
        public readonly ?string $pixCopiaECola = null,
        /** # Formato do campo chave * O campo chave determina a chave Pix registrada no DICT que será utilizada para a cobrança. Essa chave será lida pelo aplica… */
        public readonly ?string $chave = null,
        /** O campo solicitacaoPagador, opcional, determina um texto a ser apresentado ao pagador para que ele possa digitar uma informação correlata, em formato … */
        public readonly ?string $solicitacaoPagador = null,
        /** Cada respectiva informação adicional contida na lista (nome e valor) deve ser apresentada ao pagador. */
        #[ArrayOf(InfoAdicional::class)]
        public readonly array $infoAdicionais = [],
        #[ArrayOf(Pix::class)]
        public readonly array $pix = [],
    ) {}
}
