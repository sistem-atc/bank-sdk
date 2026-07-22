<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Arrecadacao;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Registro de um pagamento de arrecadação já efetuado, como devolvido dentro de
 * `regSaida` na consulta.
 *
 * Origem: GET /pagamento/arrecadacao-via-codbarras/v1/{agencia}/{conta}/{tipoConta}
 * (schema `PagamentoResponse`).
 */
final class PagamentoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $anoIncidencia = null,  // Ano de Incidência (AAAA) [max:4]
        public readonly ?int $autenticacaoBancaria = null,  // Número utilizado para os comprovantes [max:9]
        public readonly ?string $campoDigitado1 = null,  // Lacre de Conectividade [max:15]
        public readonly ?string $campoDigitado2 = null,  // Identificador [max:20]
        public readonly ?string $cnpjCEI = null,  // CNPJ ou CEI ou Identificador [max:14]
        public readonly ?string $cnpjDarf = null,  // CNPJ ou CPF Principal (Tipo de comprovante 14/15)
        public readonly ?string $cnpjFilialOutroBanco = null,  // CNPJ filial de outros bancos [max:5]
        public readonly ?string $codigoAutorizacaoOutroBanco = null,  // Código da autorização de outros bancos [max:6]
        public readonly ?string $codigoBarras = null,  // Código de barras (sem os dígitos verificadores) [max:44]
        public readonly ?string $codigoBarrasComDigito = null,  // Código de barras (com os dígitos verificadores)
        public readonly ?int $codigoEmpresaConveniada = null,  // Código da Empresa FEBRABAN (Segmento + Empresa) [max:5]
        public readonly ?int $codigoEstabelecimento = null,  // Identificador do Código do Estabelecimento do Órgão / Empresa [max:5]
        public readonly ?string $codigoMunicipio = null,  // Código do nunicípio (NNND) [max:4]
        public readonly ?int $codigoProcessamento = null,  // Código do processamento [max:6]
        public readonly ?string $codigoReceita = null,  // Campo GARE-ICMS - Empresas 5-0051 / 5-0099 / 5-0165 Campo HHMMSS -...
        public readonly ?int $codigoReceitaDarf = null,  // Código da Receita do Pagamento (NNND) (Tipo de comprovante 14, 15 e...
        public readonly ?string $competencia = null,  // Competência (MMAAAA) [max:6]
        public readonly ?int $contaDebito = null,  // Conta Debitada 1 - Conta Débito Enviada 2 - Conta Débito Linkada [m...
        public readonly ?int $contaTipo = null,  // Tipo da conta Ex. 01 - Conta corrente IB 02 - Conta de poupança 03...
        public readonly ?string $controleMensagem = null,  // 0 - Não Imprime 1 - Imprime [max:1]
        public readonly ?float $correcaoMonetaria = null,  // Valor Correção Monetária (9999999999DD) [max:12]
        public readonly ?int $cota = null,  // Informa o tipo de parcelamento do IPVA : 1 - 1a Parcela 2 - 2a Parc...
        public readonly ?string $cpfCnpjFilialOutroBanco = null,  // CNPJ/CPF filial de outros bancos [max:5]
        public readonly ?string $cpfCnpjPrincipalOutroBanco = null,  // CPF/CNPJ principal de outros bancos [max:9]
        public readonly ?string $dataDebito = null,  // Data do debito (formato: AAAA-MM-DD) [max:8]
        public readonly ?string $dataHoraTransacao = null,  // Mês / Dia / Hora da Transação (format: MMDDHHmmss) [max:10]
        public readonly ?string $dataPagamento = null,  // Data do momento do pagamento realizado no canal (formato: AAAA-MM-D...
        public readonly ?string $dataValidade = null,  // Data de validade (formato: AAAA-MM-DD) [max:8]
        public readonly ?string $dataVencimento = null,  // Data do vencimento (formato: AAAA-MM-DD) [max:8]
        public readonly ?string $dataVencimentoDarf = null,  // Data de Vencimento (formato: AAAA-MM-DD) (Tipo de comprovante 14)
        public readonly ?string $dataVencimentoOrgao = null,  // Data de Vencimento enviado pelo Órgão (formato: AAAA-MM-DD) [max:8]
        public readonly ?string $dddOutroBanco = null,  // DDD do Telefone [max:4]
        public readonly ?float $descontos = null,  // Valor do Desconto (99999999999DD) [max:13]
        public readonly ?string $descricaoCliente = null,  // Descrição do Pagamento pelo Cliente [max:32]
        public readonly ?int $descricaoDsDsiAiim = null,  // Descrição DS/DSI - AIIM
        public readonly ?string $diDSI = null,  // Campo GARE-ICMS [max:9]
        public readonly ?string $documento = null,  // Número do Documento [max:25]
        public readonly ?int $exercicio = null,  // Ano do Exercício. Se vier 0000, não existe exercício. [max:4]
        public readonly ?string $expiracaoCartao = null,  // Expiração do Cartão de Crédito (MMAAAA) [max:8]
        public readonly ?int $formaAcolhimento = null,  // 1 - Código de Barras 2 - On-Line [max:2]
        public readonly ?string $horaPagamento = null,  // Hora em que foi realizado o pagamento (format: HHMMSS)
        public readonly ?string $identidadeDocumento = null,  // Nome do Campo-Chave (Utilizando apenas para o DARF 50385) Número do...
        public readonly ?int $identificaNroDsDsiAiim = null,  // Identifica número DS/DSI - AIIM : 1 (constante) – DS/DSI 2 (constan...
        public readonly ?string $identificacaoPagamento = null,  // Descrição do Tributo [max:40]
        public readonly ?string $identificacaoVeiculo = null,  // Identificação do Veículo Se o Tipo de Veículo for 3 - Veículo Terre...
        public readonly ?float $juros = null,  // Valor dos Juros / Mora (99999999999DD) [max:13]
        public readonly ?int $mesIncidencia = null,  // Mês de Incidência (MM) [max:2]
        public readonly ?string $midia = null,  // Descrição da Midia [max:25]
        public readonly ?float $multa = null,  // Valor da Multa (99999999999DD) [max:13]
        public readonly ?string $municipio = null,  // Código da Taxa de Incêndio [max:3]
        public readonly ?string $nomeContribuinte = null,  // Nome do contribuinte enviado pela Prefeitura de São Paulo [max:40]
        public readonly ?string $nomeEmpresaConveniada = null,  // Nome da empresa conveniada
        public readonly ?string $nomePortador = null,  // Nome do Portador do Cartão de Crédito [max:35]
        public readonly ?int $nsuBanco = null,  // Número de Sequência Único no Bradesco [max:6]
        public readonly ?int $nsuProdam = null,  // NSU da Prefeitura [max:9]
        public readonly ?int $numNr = null,  // Número do NR [max:2]
        public readonly ?string $numeroCartao = null,  // Número do Cartão de Crédito [max:16]
        public readonly ?string $numeroCartaoOutroBanco = null,  // Numero do cartão de outros bancos [max:19]
        public readonly ?int $numeroContribuinte = null,  // Número de Identificação do Contribuinte [max:11]
        public readonly ?int $numeroControleDare = null,  // Número de controle do DARE
        public readonly ?int $numeroDocumento = null,  // Número do Documento (Tipo de comprovante 17/25/26)
        public readonly ?int $numeroDocumentoSefaz = null,  // Número do documento
        public readonly ?int $numeroEtiqueta = null,  // Número da etiqueta
        public readonly ?int $numeroGuia = null,  // Número da guia
        public readonly ?string $numeroIdentidade = null,  // Nome do campo chave Campo chave do documento (Utilizando apenas par...
        public readonly ?int $parcela = null,  // Número da Parcela [max:2]
        public readonly ?float $percentualCETMensal = null,  // Percentual do CET Mensal [max:5]
        public readonly ?float $percentualCETPeriodo = null,  // Percentual do CET Período [max:5]
        public readonly ?float $percentualMulta = null,  // Percentual de Multa Aplicado [max:2]
        public readonly ?string $periodoApuracao = null,  // Período de Apuração (formato: AAAA-MM-dd) (Tipo de comprovante 14/15)
        public readonly ?string $placa = null,  // Número da Placa [max:7]
        public readonly ?float $prcentualAplicado = null,  // Percentual aplicado (99DD) (Tipo de comprovante 14/15)
        public readonly ?int $quantidadeParcelas = null,  // Quantidade de parcelas [max:2]
        public readonly ?string $quantidadePontos = null,  // Quantidade de pontos acumulados pelo cartão [max:11]
        public readonly ?string $retornoCICS = null,  // Mensagem de Retorno do CICS [max:40]
        public readonly ?int $situacaoConta = null,  // Situação do Pagamento 01 - A debitar: indica que a operação está ag...
        public readonly ?string $telefoneOutroBanco = null,  // Número do Telefone [max:11]
        public readonly ?string $tipoComprovante = null,  // Identifica o tipo de comprovante que deve ser impresso: 01 - IPTU -...
        public readonly ?int $tipoIdentificacao = null,  // Tipo de Identificação : 1 (constante) – CNPJ 2 (constante) – CPF 3...
        public readonly ?int $tipoParcela = null,  // 01 - Parcela Única 02 - Pagamento Parcelado 03 - Vencimento Integra...
        public readonly ?int $tipoVeiculo = null,  // Identifica o tipo de veículo para o qual está sendo pago o IPVA: 0...
        public readonly ?string $tributo = null,  // Código do Tributo [max:2]
        public readonly ?string $uf = null,  // Campo GARE-ICMS [max:2]
        public readonly ?float $valorCETAnual = null,  // Valor CET anual [max:17]
        public readonly ?string $valorCodigoBarras = null,  // Valor Original do Código de Barras (99999999999DD) [max:13]
        public readonly ?float $valorDebito = null,  // Valor do débito (99999999999DD) [max:13]
        public readonly ?float $valorEncargo = null,  // Valor de Encargo [max:5]
        public readonly ?float $valorEncargoCartao = null,  // Valor total de encargos a serem cobrados [max:7]
        public readonly ?float $valorIOF = null,  // Valor do IOF [max:5]
        public readonly ?string $valorInformadoCliente = null,  // Valor informado S - O valor foi informado pelo cliente N - O valor...
        public readonly ?float $valorReceitaBruta = null,  // Valor da receita bruta acumulada (999999999DD) (Tipo de comprovante...
        public readonly ?float $valorTarifa = null,  // Valor da tarifa [max:5]
    ) {}
}
