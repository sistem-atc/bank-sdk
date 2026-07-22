<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Sispag;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item da lista de pagamentos SISPAG — `GET /sispag/v1/pagamentos_sispag`.
 *
 * `status` ∈ {Pendente de autorização, Pendente de efetivacao, Autorizado,
 * Efetuado, Não Efetuado, Pendente de alteração/exclusão, Rejeitado em arquivo}.
 */
final class PagamentoSispagItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idPagamento = null,
        public readonly ?string $status = null,
        public readonly ?string $motivo = null,
        public readonly ?string $nomeFavorecido = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $codBanco = null,
        public readonly ?string $numeroAgencia = null,
        public readonly ?string $numeroConta = null,
        public readonly ?string $tipoPagamento = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $valorPagamento = null,
    ) {}
}
