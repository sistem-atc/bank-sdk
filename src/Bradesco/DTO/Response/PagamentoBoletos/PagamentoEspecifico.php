<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Detalhe de UM pagamento/agendamento específico (por protocolo ou transactionId).
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-pagamento-consulta/consulta-pagamento-especifico
 * (schema TituloEspecificoResponseDTO).
 *
 * NOTA: os campos de CNPJ/CPF (`ccnpjCdentCobr`, `ccnpjSacdrAvals`,
 * `ccgcCpfSacdo`, `cnpjCpfCtoPgto`) vêm tipados como `integer` na spec e são
 * declarados `?string` aqui para preservar zeros à esquerda.
 */
final class PagamentoEspecifico implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,                        // Código do status HTTP.
        public readonly ?string $transacao = null,                  // Código da transação executada.
        public readonly ?string $mensagem = null,                   // Mensagem de retorno.
        public readonly ?string $causa = null,                      // Causa/erro técnico.
        public readonly ?int $situacaoPagamento = null,             // Situação atual do pagamento.
        public readonly ?int $transactionId = null,                 // Transaction ID enviado pelo cliente.
        public readonly ?int $numeroProtocolo = null,               // Número do protocolo do pagamento.
        public readonly ?int $dataInclTit = null,                   // Data de inclusão do título. AAAAMMDD.
        public readonly ?string $segLinhaExtr = null,               // 2ª linha de extrato / histórico.
        public readonly ?float $valorPagamento = null,              // Valor do pagamento.
        public readonly ?int $dataVencimento = null,                // Vencimento do título. AAAAMMDD.
        public readonly ?string $digitavel1 = null,                 // Linha digitável (parte 1).
        public readonly ?string $digitavel2 = null,                 // Linha digitável (parte 2).
        public readonly ?int $motNaoEfet = null,                    // Código do motivo da não efetivação.
        public readonly ?string $descrMotivo = null,                // Descrição do motivo da não efetivação.
        public readonly ?int $codBancoDestino = null,               // Código do banco de destino.
        public readonly ?string $nomeBancoDestino = null,           // Nome do banco de destino.
        public readonly ?string $icdentTitloDda = null,             // Indicador de título DDA.
        public readonly ?string $nomeFantasiaBeneficiario = null,   // Nome fantasia do beneficiário.
        public readonly ?string $ccnpjCdentCobr = null,             // CNPJ/CPF do cedente da cobrança.
        public readonly ?string $isacdrAvalsOutro = null,           // Sacador avalista.
        public readonly ?string $ccnpjSacdrAvals = null,            // CNPJ/CPF do sacador avalista.
        public readonly ?int $instRecebedora = null,                // Instituição recebedora.
        public readonly ?string $isacdo = null,                     // Sacado.
        public readonly ?string $ccgcCpfSacdo = null,               // CNPJ/CPF do sacado.
        public readonly ?float $valorTituloOritem = null,           // Valor original do título.
        public readonly ?float $valorDescontoTitulo = null,         // Valor de desconto do título.
        public readonly ?float $vabtmtTitloCalcd = null,            // Valor de abatimento calculado.
        public readonly ?float $valorBonusTitulo = null,            // Valor de bônus do título.
        public readonly ?float $valorMultaTitulo = null,            // Valor de multa do título.
        public readonly ?float $valorJurosTitulo = null,            // Valor de juros do título.
        public readonly ?float $valorLiquidoTitulo = null,          // Valor líquido do título.
        public readonly ?int $horaTransacao = null,                 // Hora da transação. HHMMSS.
        public readonly ?string $nomeInstituicaoRecebedora = null,  // Nome da instituição recebedora.
        public readonly ?string $cnpjCpfCtoPgto = null,             // CNPJ/CPF da conta de pagamento.
    ) {}
}
