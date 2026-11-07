<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Dados criados ou alterados da cobrança com vencimento via API Pix
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobVCompleta`).
 *
 * @property array<int, InfoAdicional> $infoAdicionais
 * @property array<int, Pix> $pix
 */
final class CobVCompleta implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?CobVDataVencimento $calendario = null,
        /** # Identificador da transação O campo txid determina o identificador da transação. O objetivo desse campo é ser um elemento que possibilite ao PSP do r… */
        public readonly ?string $txid = null,
        /** # O campo `revisao` Denota a revisão da cobrança. Sempre começa em zero. Sempre varia em acréscimos de 1. O incremento em uma cobrança deve ocorrer se… */
        public readonly ?int $revisao = null,
        /** Os campos aninhados sob o objeto devedor são opcionais e identificam a pessoa ou a instituição a quem a cobrança está endereçada. Não identifica, nece… */
        public readonly ?CobVDevedor $devedor = null,
        /** Os campos aninhados sob o objeto devedor são opcionais e identificam o devedor, ou seja, a pessoa ou a instituição a quem a cobrança está endereçada. … */
        public readonly ?Pessoa $recebedor = null,
        /** Identificador da localização do payload. */
        public readonly ?PayloadLocationCob $loc = null,
        public readonly ?string $status = null,
        /** Valores monetários. */
        public readonly ?CobVValor $valor = null,
        /** Este campo retorna o valor do Pix Copia e Cola correspondente à cobrança. Trata-se da sequência de caracteres que representa o BR Code. */
        public readonly ?string $pixCopiaECola = null,
        /** * O campo chave determina a chave Pix do recebedor que será utilizada para a cobrança. * Os tipos de chave podem ser: telefone, e-mail, cpf/cnpj ou EV… */
        public readonly ?string $chave = null,
        /** O campo solicitacaoPagador determina um texto a ser apresentado ao pagador para que ele possa digitar uma informação correlata, em formato livre, a se… */
        public readonly ?string $solicitacaoPagador = null,
        /** Cada respectiva informação adicional contida na lista (nome e valor) deve ser apresentada ao pagador. */
        #[ArrayOf(InfoAdicional::class)]
        public readonly array $infoAdicionais = [],
        #[ArrayOf(Pix::class)]
        public readonly array $pix = [],
    ) {}
}
