<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PixCallback` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PixCallback`).
 *
 * @property array<int, Devolucao> $devolucoes
 */
final class PixCallback implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id único para identificação do Pix Cobrança. */
        public readonly ?string $endToEndId = null,
        /** # Identificador da transação O campo txid determina o identificador da transação. O objetivo desse campo é ser um elemento que possibilite ao PSP do r… */
        public readonly ?string $txid = null,
        /** Valor do Pix. */
        public readonly ?string $valor = null,
        /** O objetivo dessa estrutura é explicar os elementos de composição do valor do Pix, incluindo informações sobre as multas, juros, descontos e abatimento… */
        public readonly ?PixValor $componentesValor = null,
        /** # Formato do campo chave * Campo chave do recebedor conforme atribuído na respectiva PACS008. * Os tipos de chave podem ser: telefone, e-mail, cpf/cnp… */
        public readonly ?string $chave = null,
        /** Horário em que o Pix foi processado no PSP. */
        public readonly ?string $horario = null,
        public readonly ?string $infoPagador = null,
        #[ArrayOf(Devolucao::class)]
        public readonly array $devolucoes = [],
        public readonly ?PixCallbackPagador $pagador = null,
    ) {}
}
