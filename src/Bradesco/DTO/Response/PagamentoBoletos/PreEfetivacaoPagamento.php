<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da PRÉ-EFETIVAÇÃO do pagamento (2º passo do fluxo).
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-pre-efetivacao/pre-efetivacao-pagamento
 * (schema PreEfetivacaoResponseDTO).
 *
 * É a simulação: devolve o valor que SERÁ debitado (`valorCobrado`) e o
 * protocolo. NÃO movimenta dinheiro — quem debita é a efetivação.
 *
 * NOTA: `linhaDigitavel2` vem tipada como `integer` na spec, mas é declarada
 * `?string` aqui para não perder zeros à esquerda.
 */
final class PreEfetivacaoPagamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,               // Código de status da operação.
        public readonly ?string $transacao = null,         // Identificador único da transação.
        public readonly ?string $mensagem = null,          // Mensagem descritiva do resultado.
        public readonly ?string $causa = null,             // Descrição da causa do erro, quando aplicável.
        public readonly ?int $indicNomeCedente = null,     // Indicador do nome do cedente.
        public readonly ?string $nomeCedente = null,       // Nome do cedente associado ao pagamento.
        public readonly ?int $nroProtocolo = null,         // Protocolo gerado para a transação.
        public readonly ?float $valorTitulo = null,        // Valor total do título.
        public readonly ?float $valorDesconto = null,      // Valor do desconto aplicado.
        public readonly ?float $valorAbatimento = null,    // Valor do abatimento aplicado.
        public readonly ?float $valorBonificacao = null,   // Valor da bonificação aplicada.
        public readonly ?float $valorMulta = null,         // Valor da multa aplicada.
        public readonly ?float $valorJuros = null,         // Valor dos juros aplicados.
        public readonly ?float $valorCobrado = null,       // Valor total cobrado no título.
        public readonly ?int $dataVctoTitlo = null,        // Vencimento do título. Formato AAAAMMDD.
        public readonly ?int $dataQuitacao = null,         // Data de quitação. Formato AAAAMMDD.
        public readonly ?string $linhaDigitavel1 = null,   // Primeira parte da linha digitável.
        public readonly ?string $linhaDigitavel2 = null,   // Segunda parte da linha digitável.
    ) {}
}
