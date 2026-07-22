<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Posicao consolidada em COE.
 *
 * Origem: GET /managers-position-mgmt/v1/consolidatedposition/coe/{cpfCnpj}/{accountCode}
 */
final class PosicaoCoeResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Metadados da consulta. */
        public readonly ?Meta $meta = null,
        /** Status code da resposta. */
        public readonly ?int $statusCode = null,
        /** Erros retornados pelo backend. @var array<int, ErroApi> */
        #[ArrayOf(ErroApi::class)]
        public readonly array $errors = [],
        /** Bloco de status da resposta. */
        public readonly ?RespostaBase $response = null,
        /** Codigo de retorno. */
        public readonly ?int $code = null,
        /** Descricao do retorno. */
        public readonly ?string $description = null,
        /** COEs em custodia. @var array<int, PosicaoCoeItem> */
        #[ArrayOf(PosicaoCoeItem::class)]
        public readonly array $consolidatedPosition = [],
        /** Mensagem adicional do backend. */
        public readonly ?MensagemCodigo $message = null,
    ) {}
}
