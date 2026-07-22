<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da exclusão (cancelamento) de um agendamento de pagamento.
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-excluir-agendamento/exclusao/executar
 * (schema ExcluirAgendamentoResponseDTO).
 */
final class ExclusaoAgendamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,          // Código do status HTTP.
        public readonly ?string $transacao = null,    // Código da transação executada.
        public readonly ?string $mensagem = null,     // Mensagem de retorno.
        public readonly ?string $causa = null,        // Código/mensagem técnica quando 400, 412 ou 500.
    ) {}
}
