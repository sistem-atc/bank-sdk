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
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobGerada`).
 *
 * @property array<int, InfoAdicional> $infoAdicionais
 */
final class CobGerada implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Não é permitido que o campo devedor.cpf e campo devedor.cnpj estejam preenchidos ao mesmo tempo. Se o campo devedor.nome está preenchido, então deve e… */
        public readonly ?Pessoa $devedor = null,
        /** Identificador da localização do payload. */
        public readonly ?PayloadLocation $loc = null,
        /** Localização do Payload a ser informada na criação da cobrança. */
        public readonly ?string $location = null,
        public readonly ?string $status = null,
        /** valores monetários referentes à cobrança. */
        public readonly ?CobValor $valor = null,
        /** Os campos aninhados sob o identificador calendário organizam informações a respeito de controle de tempo da cobrança. */
        public readonly ?Calendario $calendario = null,
        public readonly ?string $txid = null,
        /** # O campo `revisao` Denota a revisão da cobrança. Sempre começa em zero. Sempre varia em acréscimos de 1. O incremento em uma cobrança deve ocorrer se… */
        public readonly ?int $revisao = null,
        /** Este campo retorna o valor do Pix Copia e Cola correspondente à cobrança. Trata-se da sequência de caracteres que representa o BR Code. */
        public readonly ?string $pixCopiaECola = null,
        /** * O campo chave determina a chave Pix do recebedor que será utilizada para a cobrança. * Os tipos de chave podem ser: telefone, e-mail, cpf/cnpj ou EV… */
        public readonly ?string $chave = null,
        /** O campo solicitacaoPagador determina um texto a ser apresentado ao pagador para que ele possa digitar uma informação correlata, em formato livre, a se… */
        public readonly ?string $solicitacaoPagador = null,
        /** Cada respectiva informação adicional contida na lista (nome e valor) deve ser apresentada ao pagador. */
        #[ArrayOf(InfoAdicional::class)]
        public readonly array $infoAdicionais = [],
    ) {}
}
