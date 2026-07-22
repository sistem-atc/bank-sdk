<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Arrecadacao;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da pré-confirmação (tipoRegistro=0) e da efetivação (tipoRegistro=1)
 * do pagamento de conta de consumo/tributo.
 *
 * ⚠️ MOVIMENTA DINHEIRO: na efetivação, `retorno` = 0 indica sucesso e
 * `autenticacaoBancaria` é o número do comprovante.
 *
 * Origem: POST /pagamento/arrecadacao-via-codbarras/v1/pagamentoContaConsumo
 * (schema `PagamentoEfetivacaoResponse`).
 */
final class PagamentoEfetivacaoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $agencia = null,  // Código da Agência
        public readonly ?int $autenticacaoBancaria = null,  // Número utilizado para os comprovantes
        public readonly ?int $banco = null,  // Número do Banco (Fixo: 237)
        public readonly ?string $campoChaveIdentificacaoDocumento = null,  // Campo chave do documento Nome do campo chave (Utilizando apenas par...
        public readonly ?string $cnpjCEI = null,  // CNPJ ou CEI ou Identificador
        public readonly ?string $cnpjDarf = null,  // CNPJ ou CPF Principal (Tipo de comprovante 14/15)
        public readonly ?string $codigoAutorizacaoOutrosBancos = null,  // Código de autorização outros bancos
        public readonly ?string $codigoBarras = null,  // Código de barras (com os dígitos verificadores)
        public readonly ?int $codigoEmpresaDebitoAutomatico = null,  // Código da empresa do Débito Automático
        public readonly ?int $codigoMensagemHsbcNet = null,  // Código Mensagem HSBC-NET
        public readonly ?int $codigoMunicipio = null,  // Número do Código do Município (NNND)
        public readonly ?int $codigoReceita = null,  // Codigo Receita Campo GARE-ICMS - Empresas 5-0051 / 5-0099 / 5-0165...
        public readonly ?int $codigoReceitaDarf = null,  // Código da receita DARF (Tipo de comprovante 14/15)
        public readonly ?int $codigoTributo = null,  // Código do tributo
        public readonly ?string $competencia = null,  // Competência (formato: MMAAAA)
        public readonly ?int $conta = null,  // Número da Conta
        public readonly ?int $contaDebito = null,  // Conta de Débito
        public readonly ?int $cota = null,  // Informa o tipo de parcelamento do IPVA : 1 - 1a Parcela 2 - 2a Parc...
        public readonly ?string $cpfCnpj = null,  // Número do CPF/CNPJ
        public readonly ?string $dataDebito = null,  // Data do débito (formato: AAAA-MM-DD)
        public readonly ?string $dataPagamento = null,  // Data do momento do pagamento realizado no canal (formato: AAAA-MM-D...
        public readonly ?string $dataValidade = null,  // Data de validade (formato: AAAA-MM-DD)
        public readonly ?string $dataVencimento = null,  // Data do vencimento (formato: AAAA-MM-DD)
        public readonly ?string $dataVencimentoDarf = null,  // Data de Vencimento (formato: AAAA-MM-DD) (Tipo de comprovante 14)
        public readonly ?string $dddTelefone = null,  // DDD do telefone
        public readonly ?string $descricaoCliente = null,  // Descrição do pagamento pelo Cliente
        public readonly ?int $descricaoDsDsiAiim = null,  // Número DS/DSI - AIIM
        public readonly ?int $diDSI = null,  // Campo GARE-ICMS
        public readonly ?int $exercicio = null,  // Ano do Exercício. Se vier 0000, não existe exercício.
        public readonly ?string $expiracaoCartao = null,  // Expiração do cartão (format: MM-AAAA)
        public readonly ?string $formaDigitacao = null,  // Forma de digitação documento
        public readonly ?string $formatoDocumento = null,  // Formato do documento
        public readonly ?string $horaPagamento = null,  // Hora em que foi realizado o pagamento (format: HHMMSS)
        public readonly ?int $identificaNroDsDsiAiim = null,  // Identifica número DS/DSI - AIIM: 1 (constante) – DS/DSI 2 (constant...
        public readonly ?string $identificacaoCampoChave = null,  // Nome do campo chave Campo chave do documento (Utilizando apenas par...
        public readonly ?string $identificacaoPagamento = null,  // Descrição do Tributo
        public readonly ?string $identificacaoVeiculo = null,  // Identificação do Veículo Se o Tipo de Veículo for 3 - Veículo Terre...
        public readonly ?float $limiteCartao = null,  // Limite Disponível do Cartão de Crédito
        public readonly ?string $linhaDetalhe = null,  // Linha detalhe
        public readonly ?int $municipio = null,  // Código da Taxa de Incêndio
        public readonly ?string $nomeCliente = null,  // Nome do Cliente
        public readonly ?string $nomeEmpresaConveniada = null,  // Nome da empresa conveniada
        public readonly ?string $nomeFantasia = null,  // Nome Fantasia
        public readonly ?string $nomeFantasiaDebitoAutomatico = null,  // Nome fantasia do débito automático
        public readonly ?string $nomePortadorCartao = null,  // Nome Do Portador do Cartão de Crédito
        public readonly ?string $numeroCartao = null,  // Numero do Cartão de Crédito
        public readonly ?string $numeroCartaoOutrosBancos = null,  // Número do cartão outros bancos
        public readonly ?int $numeroControleDare = null,  // Número de controle do DARE
        public readonly ?int $numeroDocumento = null,  // Número do Documento (Tipo de comprovante 17/25/26)
        public readonly ?int $numeroDocumentoSefaz = null,  // Número do documento
        public readonly ?int $numeroEtiqueta = null,  // Número da etiqueta
        public readonly ?int $numeroGuia = null,  // Número da guia
        public readonly ?int $numeroNr = null,  // Número NR
        public readonly ?int $numeroParcela = null,  // Número da Parcela
        public readonly ?int $numeroPeriferico = null,  // Número de Periférico
        public readonly ?int $numeroSequencia = null,  // Número Sequência/Transação
        public readonly ?string $obrigaDigitarValorDebito = null,  // Obriga digitar valor do debito S - É obrigatório digitar o valor do...
        public readonly ?float $percentualCETMensal = null,  // Percentual do CET mensal
        public readonly ?float $percentualCETPeriodo = null,  // Percentual do CET período
        public readonly ?float $percentualEncargo = null,  // Percentual dos encargos
        public readonly ?float $percentualIOF = null,  // Percentual do IOF
        public readonly ?float $percentualTarifa = null,  // Percentual da Tarifa
        public readonly ?string $periodoApuracao = null,  // Período de Apuração (formato: AAAA-MM-DD) (Tipo de comprovante 14/15)
        public readonly ?string $placa = null,  // Número da Placa
        public readonly ?float $prcentualAplicado = null,  // Percentual aplicado (99DD) (Tipo de comprovante 14/15)
        public readonly ?int $quantidadePontosAcumuladosCartao = null,  // Quantidade de pontos acumulados pelo cartão
        public readonly ?int $retorno = null,  // Código de Retorno da execução (ver item 6.3 “Códigos de Retorno da...
        public readonly ?string $senha = null,  // Senha do Cartão de Crédito
        public readonly ?int $sqlCode = null,  // Código de erro retornado pelo gerenciador de banco de dados DB2
        public readonly ?int $statusDebitoAutomatico = null,  // Status do Débito Automático 0 - Empresa cadastrada no débito automá...
        public readonly ?float $taxaJuros = null,  // Percentual da taxa de juros
        public readonly ?string $telefone = null,  // Número do telefone
        public readonly ?int $tipoComprovante = null,  // Identifica o tipo de conprovante que deve ser impresso 01 - IPTU -...
        public readonly ?int $tipoConta = null,  // Tipo da conta 01 - Conta Corrente 02 - Poupança 03 - INSS 04 - Cart...
        public readonly ?int $tipoDispositivo = null,  // Tipo de dispositivo
        public readonly ?int $tipoIdentificacao = null,  // Tipo de Identificação : 1 (constante) – CNPJ 2 (constante) – CPF 3...
        public readonly ?string $tipoRegistro = null,  // Tipo de registro 0 = Consulta 1 = Inclusão
        public readonly ?int $tipoServico = null,  // Tipo de Serviço da Empresa (vide descrição da transação pcon2360)
        public readonly ?int $tipoVeiculo = null,  // Identifica o tipo de veículo para o qual está sendo pago o IPVA: 0...
        public readonly ?string $tituloDebitoAutomatico = null,  // Tipo de débito automático
        public readonly ?string $tituloPCON = null,  // Título Pcon
        public readonly ?float $totalTransacao = null,  // Valor total da transação
        public readonly ?string $uf = null,  // Campo GARE-ICMS
        public readonly ?float $valorCETAnual = null,  // Valor do CET anual
        public readonly ?float $valorDesconto = null,  // Valor do Desconto (99999999999DD)
        public readonly ?float $valorEncargo = null,  // Valor de Encargo
        public readonly ?float $valorIOF = null,  // Valor do IOF
        public readonly ?float $valorJurosMora = null,  // Valor dos Juros / Mora (99999999999DD)
        public readonly ?float $valorMulta = null,  // Valor da Multa (99999999999DD)
        public readonly ?float $valorPago = null,  // Valor do Débito (99999999999DD)
        public readonly ?float $valorReceitaBruta = null,  // Valor da receiota bruta acumulada (Tipo de comprovante 14/15) (form...
        public readonly ?float $valorTarifa = null,  // Valor da Tarifa
        public readonly ?float $valorTotalEncargosASeremCobrados = null,  // Valor total de encargos a serem cobrados
        public readonly ?float $valorTributo = null,  // Valor Original do Código de Barras (99999999999DD)
        public readonly ?string $vencimentoFaturaCartao = null,  // Vencimento da fatura do cartão (formato: DD-MM-AAAA)
    ) {}
}
