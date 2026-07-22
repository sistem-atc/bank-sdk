<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Parâmetros e limites de pagamento de boletos de cobrança da conta.
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-parametros-pgto/executar
 * (schema ConsultaParametrosResponseDTO).
 *
 * É a consulta que diz até quanto e até que horário a conta pode pagar —
 * cheque antes de disparar a efetivação.
 */
final class ParametrosPagamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,                  // Código de status da operação.
        public readonly ?string $transacao = null,            // Identificador da transação.
        public readonly ?string $mensagem = null,             // Mensagem descritiva do resultado.
        public readonly ?string $causa = null,                // Código e descrição da causa do resultado.
        public readonly ?int $horaEncerBrad = null,           // Horário de encerramento p/ transações Bradesco.
        public readonly ?int $horaEncerOutros = null,         // Horário de encerramento p/ outros bancos.
        public readonly ?int $qtdAnosLim = null,              // Quantidade de anos limite para consulta.
        public readonly ?float $limDinheiroBrad = null,       // Limite de pagamento em dinheiro no Bradesco.
        public readonly ?float $limDebCtaBrad = null,         // Limite de débito em conta no Bradesco.
        public readonly ?float $limChequeBrad = null,         // Limite de pagamento com cheque no Bradesco.
        public readonly ?float $limDinheiroOutr = null,       // Limite de pagamento em dinheiro em outros bancos.
        public readonly ?float $limDebCtaOutr = null,         // Limite de débito em conta em outros bancos.
        public readonly ?float $limChequeOutr = null,         // Limite de pagamento com cheque em outros bancos.
        public readonly ?float $limMidBrad = null,            // Limite de pagamento com mídia no Bradesco.
        public readonly ?float $limMidOutr = null,            // Limite de pagamento com mídia em outros bancos.
        public readonly ?float $limBancoBrad = null,          // Limite total de pagamento no Bradesco.
        public readonly ?float $limBancoOutr = null,          // Limite total de pagamento em outros bancos.
        public readonly ?float $limDispMidBrad = null,        // Limite disponível p/ pagamento com mídia no Bradesco.
        public readonly ?float $limDispMidOutr = null,        // Limite disponível p/ pagamento com mídia em outros bancos.
        public readonly ?float $limDispBcoBrad = null,        // Limite disponível para pagamento no Bradesco.
        public readonly ?float $limDispBcoOutr = null,        // Limite disponível para pagamento em outros bancos.
        public readonly ?float $limDispTotalBrad = null,      // Limite total disponível no Bradesco.
        public readonly ?float $limDispTotalOutr = null,      // Limite total disponível em outros bancos.
        public readonly ?int $horaInicio = null,              // Horário de início das operações.
        public readonly ?int $horaFim = null,                 // Horário de fim das operações.
        public readonly ?string $exibeGradeTed = null,        // Indicador de exibição da grade TED.
        public readonly ?float $parmVlrSuperior = null,       // Parâmetro de valor superior permitido.
        public readonly ?int $horaInicioTed = null,           // Horário de início para TED.
        public readonly ?int $horaFimTed = null,              // Horário de fim para TED.
        public readonly ?string $pagtCartaoCredt = null,      // Indicador de pagamento com cartão de crédito.
    ) {}
}
