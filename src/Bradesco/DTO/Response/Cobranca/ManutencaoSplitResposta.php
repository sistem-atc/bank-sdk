<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da manutenção (inclusão/alteração/exclusão/cancelamento) do rateio
 * de crédito de um título.
 * Origem: POST /boleto/cobranca-manutencao-split/v1/manutencao-rateio-credito
 */
final class ManutencaoSplitResposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código do Status HTTP */
        public readonly ?int $status = null,
        /** Código da Transação executada. Padrão: CBTTIAGQ */
        public readonly ?string $transacao = null,
        /** Mensagem de retorno: Quando o campo status for 400, 412 ou 500 serão demonstrados código e mensagem de erro técnica gerado no mainframe/API. Quando o campo status for 200 serão demonstrados código e mensagem de sucesso */
        public readonly ?string $mensagem = null,
        /** Quando status for 400, 412 ou 500 serão formatados código e mensagem de erro técnica gerado no mainframe/API */
        public readonly ?string $causa = null,
        /** Total de ocorrências na lista de rateio */
        public readonly ?int $qlistaRteio = null,
        #[ArrayOf(RateioCreditoResultado::class)] public readonly array $listaRteio = [],
    ) {}
}
