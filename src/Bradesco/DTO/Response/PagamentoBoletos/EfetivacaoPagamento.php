<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Comprovante da EFETIVAÇÃO do pagamento (3º e último passo do fluxo).
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-efetivacao/solicitacao/executar
 * (schema EfetivacaoResponseDTO).
 *
 * ⚠️ Este é o retorno da chamada que DEBITA a conta (ou agenda o débito).
 * `nroProtocolo` é o identificador do pagamento/agendamento — guarde-o: é ele
 * que permite consultar, alterar ou excluir depois.
 *
 * NOTA: campos de CPF/CNPJ e `linhaDigitavel2` vêm tipados como `integer` na
 * spec e são declarados `?string` aqui (zero à esquerda).
 */
final class EfetivacaoPagamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,                       // Código do status HTTP.
        public readonly ?string $transacao = null,                 // Código da transação executada.
        public readonly ?string $mensagem = null,                  // Mensagem de retorno.
        public readonly ?string $causa = null,                     // Código/mensagem técnica quando 400, 412 ou 500.
        public readonly ?int $indicNomeCedente = null,             // Indicador de existência do nome do cedente.
        public readonly ?string $nomeCedente = null,               // Razão social do beneficiário.
        public readonly ?int $nroProtocolo = null,                 // Número do protocolo CBCA do pagamento.
        public readonly ?float $valorTitulo = null,                // Valor do título. 9(13)V9(2).
        public readonly ?float $valorDesconto = null,              // Valor do desconto.
        public readonly ?float $valorAbatimento = null,            // Valor do abatimento.
        public readonly ?float $valorBonificacao = null,           // Valor da bonificação.
        public readonly ?float $valorMulta = null,                 // Valor da multa.
        public readonly ?float $valorJuros = null,                 // Valor dos juros.
        public readonly ?float $valorCobrado = null,               // Valor devido calculado (o que foi debitado).
        public readonly ?int $dataVctoTitlo = null,                // Vencimento. Formato AAAAMMDD.
        public readonly ?int $dataQuitacao = null,                 // Data de quitação. Formato AAAAMMDD.
        public readonly ?string $linhaDigitavel1 = null,           // Linha digitável editada p/ o comprovante.
        public readonly ?string $linhaDigitavel2 = null,           // Linha digitável do fator e valor do título.
        public readonly ?int $liberaCredito = null,                // Libera o crédito na data do pagamento?
        public readonly ?int $codigoCip = null,                    // Código da CIP.
        public readonly ?int $bcoCtlzProt = null,                  // Banco centralizador de protesto.
        public readonly ?int $agnCtlzProt = null,                  // Agência centralizadora de protesto.
        public readonly ?string $nomeAgnCtlzProt = null,           // Nome da agência centralizadora de protesto.
        public readonly ?string $endAgnCtlzProt = null,            // Endereço da agência centralizadora de protesto.
        public readonly ?int $dataInstrProt = null,                // Data de instrução de protesto. AAAAMMDD.
        public readonly ?int $dataEnvioCart = null,                // Data de envio ao cartório. AAAAMMDD.
        public readonly ?string $protocoloCart = null,             // Número do protocolo do cartório.
        public readonly ?string $numeroCart = null,                // Número do cartório.
        public readonly ?string $cnpjCpfSacado = null,             // CNPJ/CPF do sacado pagador.
        public readonly ?string $nomeCliDebito = null,             // Nome do sacado (débito em conta / cheque Bradesco).
        public readonly ?float $valorPgtoMin = null,               // Valor mínimo para pagamento.
        public readonly ?float $valorPgtoMax = null,               // Valor máximo para pagamento.
        public readonly ?int $indComSemRegis = null,               // Indicador de título registrado.
        public readonly ?string $indTitVencd = null,               // Indicador de título sem registro vencido.
        public readonly ?string $aceitaPgtoCheque = null,          // Cedente aceita pagamento em cheque?
        public readonly ?string $nomeFantasiaBenef = null,         // Nome fantasia do beneficiário.
        public readonly ?string $cpfCnpjBenef = null,              // CNPJ/CPF do beneficiário do título.
        public readonly ?string $cpfCnpjFilialBenef = null,        // Filial do CNPJ/CPF do beneficiário.
        public readonly ?string $cpfCnpjCntrlBenef = null,         // Controle do CNPJ/CPF do beneficiário.
        public readonly ?string $nomeSacadorAval = null,           // Nome do sacador avalista.
        public readonly ?string $cpfCnpjAval = null,               // CNPJ/CPF do sacador avalista.
        public readonly ?string $cpfCnpjFilialAval = null,         // Filial do CNPJ/CPF do sacador avalista.
        public readonly ?string $cpfCnpjCntrlAval = null,          // Controle do CNPJ/CPF do sacador avalista.
        public readonly ?string $nomePagador = null,               // Nome do pagador.
        public readonly ?string $nomeBancoBenef = null,            // Nome do banco beneficiário do título.
        public readonly ?string $nomeBancoReceb = null,            // Nome do banco recebedor do título.
        public readonly ?int $codBancoReceb = null,                // Código do banco recebedor do pagamento.
        public readonly ?string $cpfCnpjPgto = null,               // CNPJ/CPF do portador do título.
        public readonly ?string $cpfCnpjFilialPgto = null,         // Filial do CNPJ/CPF do portador.
        public readonly ?string $cpfCnpjCntrlPgto = null,          // Controle do CNPJ/CPF do portador.
        public readonly ?string $indicPagtoContg = null,           // Indicador de pagamento contingenciado.
    ) {}
}
