<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item da lista de pagamentos devolvidos (schema PagamentoDevolvidoVO).
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-lista-pagamento-devolvido/listar
 *
 * Pagamento devolvido = débito que não se efetivou; `motivoNaoEfetivacao`
 * explica o porquê.
 */
final class PagamentoDevolvido implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dataDevolucaoPagto = null,           // Data da devolução. AAAAMMDD.
        public readonly ?string $dataPagamento = null,                // Data do pagamento. AAAAMMDD.
        public readonly ?int $protocoloPagamento = null,              // Protocolo gerado no agendamento/pagamento.
        public readonly ?int $idInformadoAPI = null,                  // transactionId informado pela API.
        public readonly ?float $valorInformado = null,                // Valor informado para o pagamento.
        public readonly ?string $linhaDigitavelTituloCobranca = null, // Linha digitável do título devolvido.
        public readonly ?int $motivoNaoEfetivacao = null,             // Código do motivo da não efetivação.
        public readonly ?string $descricaoMotivoNaoEfetivacao = null, // Descrição do motivo da não efetivação.
    ) {}
}
