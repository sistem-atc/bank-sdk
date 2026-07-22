<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da alteração de um agendamento de pagamento (data e/ou valor).
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-alterar-agendamento/alteracao/executar
 * (schema AlteracaoAgendamentoResponseDTO).
 *
 * ⚠️ Altera um débito futuro já agendado. Se o valor informado não for
 * permitido, `valorPagamento` volta com o valor que o banco aceitou.
 */
final class AlteracaoAgendamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,               // Código do status HTTP.
        public readonly ?string $transacao = null,         // Código da transação executada.
        public readonly ?string $mensagem = null,          // Mensagem de retorno.
        public readonly ?string $causa = null,             // Código/mensagem técnica quando 400, 412 ou 500.
        public readonly ?int $dataPagamento = null,        // Data do pagamento do agendamento. AAAAMMDD.
        public readonly ?float $valorPagamento = null,     // Valor efetivamente aceito para o pagamento.
        public readonly ?string $descricaoBoleto = null,   // Descrição do boleto.
    ) {}
}
