<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item da lista de agendamentos/pagamentos (schema AgendamentoVO).
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-agendamentos-pgto/listar
 */
final class Agendamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $bancoEmissor = null,                    // Banco emissor do título.
        public readonly ?string $dataPagamento = null,                // Data de pagamento. AAAAMMDD.
        public readonly ?string $dataVencimento = null,               // Data de vencimento. AAAAMMDD.
        public readonly ?string $descricaoMotivoNaoEfetivacao = null, // Descrição do motivo da não efetivação.
        public readonly ?int $idInformadoAPI = null,                  // transactionId informado pela API.
        public readonly ?int $motivoNaoEfetivacao = null,             // Motivo da não efetivação (3, 9, 12).
        public readonly ?int $protocoloPagamento = null,              // Protocolo do pagamento do título.
        public readonly ?float $valorCalculado = null,                // Valor calculado para pagamento.
        public readonly ?float $valorInformado = null,                // Valor informado do título.
    ) {}
}
