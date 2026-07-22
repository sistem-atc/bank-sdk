<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da validação do título a pagar (1º passo do fluxo de pagamento).
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-valida-titulo-pagamento/validaTituloPagamento
 * (schema ValidaTituloPagamentoResponseDTO).
 *
 * Traz o título "aberto": beneficiário, vencimento calculado, valor cobrado
 * (com desconto/multa/juros) e as regras de valor (mín./máx., se aceita valor
 * diferente, se aceita pagamento parcial).
 *
 * NOTA: campos de CPF/CNPJ que a spec tipa como `integer` foram declarados
 * `?string` — documento com zero à esquerda perderia dígito no cast p/ int.
 */
final class ValidacaoTitulo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,                        // Código do status HTTP.
        public readonly ?string $transacao = null,                  // Código da transação executada.
        public readonly ?string $mensagem = null,                   // Mensagem de retorno.
        public readonly ?string $causa = null,                      // Código/mensagem técnica quando 400, 412 ou 500.
        public readonly ?int $dataVencimento = null,                // Vencimento calculado. Formato AAAAMMDD.
        public readonly ?int $fatorVencimento = null,               // Fator de vencimento do título calculado.
        public readonly ?int $codBancoConsulta = null,              // Código do banco emissor do título.
        public readonly ?string $nmBanco = null,                    // Nome do banco emissor do título.
        public readonly ?string $nmCedente = null,                  // Nome do cedente do título.
        public readonly ?string $tituloDda = null,                  // Título é DDA? S/N.
        public readonly ?string $pgtoCartaoCred = null,             // Aceita pagamento com cartão de crédito? S/N.
        public readonly ?float $valorTitulo = null,                 // Valor do título. Formato 9(13)V99.
        public readonly ?string $consultaCip = null,                // Precisa consultar a CIP para obter os dados? S/N.
        public readonly ?int $tempoLimiteCip = null,                // Segundos a aguardar pela resposta da CIP.
        public readonly ?string $numeroCtrlCip = null,              // Número de controle CIP da requisição.
        public readonly ?int $pagamentoValorDiferentes = null,      // 1-qualquer valor; 2-entre mín/máx; 3-não aceita diferente; 4-...
        public readonly ?string $codBarras = null,                  // Código de barras correspondente (quando entrou linha digitável).
        public readonly ?string $cnpjSacadoObri = null,             // Obrigatório informar CPF/CNPJ do sacado.
        public readonly ?string $cnpjCpfCedente = null,             // CPF/CNPJ do cedente.
        public readonly ?string $cnpjSacado = null,                 // CPF/CNPJ do sacado.
        public readonly ?string $nomeSacado = null,                 // Nome do sacado.
        public readonly ?float $valorDesconto = null,               // Valor de desconto.
        public readonly ?float $valorAbat = null,                   // Valor de abatimento.
        public readonly ?float $valorBonifi = null,                 // Valor de bonificação.
        public readonly ?float $valorMulta = null,                  // Valor de multa.
        public readonly ?float $valorJuros = null,                  // Valor de juros.
        public readonly ?float $valorCobrado = null,                // Valor cobrado.
        public readonly ?float $valorMin = null,                    // Valor mínimo aceito.
        public readonly ?float $valorMax = null,                    // Valor máximo aceito.
        public readonly ?string $cindcdAltVlr = null,               // Autoriza pagar com valor diferente do calculado.
        public readonly ?string $permitePgtoParcial = null,         // Permite pagamento parcial? S/N.
        public readonly ?string $especDocto = null,                 // Espécie do título.
        public readonly ?string $nomeFantasiaBeneficiario = null,   // Nome fantasia do beneficiário.
        public readonly ?string $pagadorFinal = null,               // Nome do pagador final.
    ) {}
}
