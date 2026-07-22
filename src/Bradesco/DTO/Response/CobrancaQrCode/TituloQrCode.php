<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Título de cobrança com QR Code consultado (traz também a 2ª via: código de
 * barras, linha digitável, EMV e imagem do QR Code em base64).
 *
 * Endpoint: POST /boleto-hibrido/cobranca-consulta-titulo/v1/consultar
 */
final class TituloQrCode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $aceite = null, // ACEITE DO TITULO
        public readonly ?float $acessEsc = null, // ACESSORIA ESCOLAR
        public readonly ?int $ageCentr = null, // AGENCIA CENTRAL
        public readonly ?int $ageProc = null, // AGENCIA DE PROCESSAMENTO
        public readonly ?int $agenDepos = null, // AGENCIA DEPOSITANTE
        public readonly ?int $agenOper = null, // AGENCIA OPERADORA
        public readonly ?int $agencCred = null, // AGENCIA DE CREDITO
        public readonly ?string $baiCedente = null, // BAIRRO DO CEDENTE
        public readonly ?string $baiSacado = null, // BAIRRO DO SACADO
        public readonly ?string $base64 = null, // IMAGEM QRCODE EM BASE64
        public readonly ?int $bcoCentr = null, // BANCO CENTRAL
        public readonly ?int $bcoDepos = null, // BANCO DEPOSITANTE
        public readonly ?int $bcoProc = null, // BANCO DE PROCESSAMENTO
        public readonly ?string $cebp = null, // CEBP
        public readonly ?int $cense = null, // CENSE
        public readonly ?int $cepEndCed = null, // CEP DO CEDENTE
        public readonly ?int $cepSacado = null, // CEP DO SACADO
        public readonly ?int $cepSacador = null, // CEP DO SACADOR
        public readonly ?int $cepcCedente = null, // COMPLEMENTO DO CEP DO CEDENTE
        public readonly ?string $cepcSacado = null, // COMPLEMENTO DO CEP DO SACADO
        public readonly ?int $cepcSacador = null, // COMPLEMENTO DO CEP DO SACADOR
        public readonly ?string $cidCedente = null, // CIDADE DO CEDENTE
        public readonly ?string $cidSacado = null, // CIDADE DO SACADO
        public readonly ?string $cidSacador = null, // CIDADE DO SACADOR
        public readonly ?int $cip = null, // CIP
        public readonly ?float $cnpjCpfCedente = null, // CPF/CNPJ DO CEDENTE
        public readonly ?float $cnpjSacado = null, // CNPJ DO SACADO
        public readonly ?float $cnpjSacador = null, // CNPJ DO SACADOR
        public readonly ?int $codBaixa = null, // CODIGO DA BAIXA
        public readonly ?string $codBarras = null, // CODIGO DE BARRAS
        public readonly ?int $codComisPerm = null, // TIPO DO JUROS 1 - VALOR 2 - PERCENTUAL
        public readonly ?int $codInscrProt = null, // CODIGO DE INSCRICAO PROTOCOLO
        public readonly ?string $codMensagem = null, // CÓDIGO MENSAGEM RETORNO MAINFRAME
        public readonly ?int $codStatus = null, // SITUACAO DO TITULO
        public readonly ?int $codValDe1 = null, // TIPO DO DESCONTO 1 - VALOR 2- PERCENTUAL
        public readonly ?int $codValDe2 = null, // TIPO DO DESCONTO 1 - VALOR 2 - PERCENTUAL
        public readonly ?int $codValDe3 = null, // TIPO DO DESCONTO 1 - VALOR 2 - PERCENTUAL
        public readonly ?int $codValMul = null, // TIPO DA MULTA 1 - VALOR 2 - PERCENTUAL
        public readonly ?string $comEndCed = null, // COMPLEMENTO DO ENDERECO DO CEDENTE
        public readonly ?string $corige35 = null, // CODIGO DE ORIGEM 35
        public readonly ?float $ctaCred = null, // CONTA DE CREDITO
        public readonly ?int $ctpoVencto = null, // TIPO DE VENCIMENTO
        public readonly ?string $ctrlPartic = null, // CONTROLE DO PARTICIPANTE
        public readonly ?string $dataCartor = null, // DATA CARTORIO
        public readonly ?string $dataDesc1 = null, // DATA DO PRIMEIRO DESCONTO(DDMMAAAA)
        public readonly ?string $dataDesc2 = null, // DATA DO SEGUNDO DESCONTO(DDMMAAAA)
        public readonly ?string $dataDesc3 = null, // DATA DO TERCEIRO DESCONTO(DDMMAAAA)
        public readonly ?string $dataEmis = null, // DATA DE EMISSAO DO TITULO(DDMMAAAA)
        public readonly ?int $dataImpressao = null, // DATA DA IMPRESSAO (DDMMAAAA)
        public readonly ?string $dataInstr = null, // DATA DE PROTESTO
        public readonly ?string $dataLimitePgt = null, // DATA LIMITE DE PAGAMENTO(DDMMAAAA)
        public readonly ?string $dataMulta = null, // DATA DA MULTA
        public readonly ?string $dataPedSus = null, // DATA DE PEDIDO DE SUSTENTACAO
        public readonly ?string $dataPerm = null, // DATA DE JUROS
        public readonly ?string $dataReg = null, // DATA DE REGISTRO DO TITULO(DDMMAAAA)
        public readonly ?string $dataSust = null, // DATA DA SUSTENTACAO
        public readonly ?string $dataVencto = null, // DATA DE VENCIMENTO DO TITULO(DD/MM/AAAA)
        public readonly ?string $dataVenctoBol = null, // DATA DE VENCIMENTO DO BOLETO(DDMMAAAA)
        public readonly ?string $debitoAuto = null, // DEBITO AUTOMATICO
        public readonly ?string $descBaixa = null, // DESCRICAO DA BAIXA
        public readonly ?string $descrDesc1 = null, // DESCRICAO DO PRIMEIRO DESCONTO
        public readonly ?string $descrDesc2 = null, // DESCRICAO DO SEGUNDO DESCONTO
        public readonly ?string $descrDesc3 = null, // DESCRICAO DO TERCEIRO DESCONTO
        public readonly ?string $descrEspec = null, // DESCRICAO DA ESPECIE
        public readonly ?string $descrMoeda = null, // DESCRICAO DA MOEDA
        public readonly ?string $descrMulta = null, // DESCRICAO DA MULTA
        public readonly ?float $despCart = null, // DESPESA CARTEIRA
        public readonly ?int $diasComisPerm = null, // QUANTIDADE DE DIAS APOS VENCIMENTO, APLICAR JUROS
        public readonly ?int $diasJuros = null, // DIAS DE JUROS
        public readonly ?int $diasMulta = null, // DIAS DA MULTA
        public readonly ?int $diasProt = null, // DIAS DE PROTESTO
        public readonly ?string $digCred = null, // DIGITO DA CONTA DE CREDITO
        public readonly ?int $dtBaixa = null, // DATA DA BAIXA
        public readonly ?int $dtPagto = null, // DATA DO PAGAMENTO
        public readonly ?string $endCedente = null, // ENDERECO DO CEDENTE
        public readonly ?string $endSacado = null, // ENDERECO DO SACADO
        public readonly ?string $endSacador = null, // ENDERECO DO SACADOR
        public readonly ?string $enderecoEma = null, // E-MAIL DO SACADO
        public readonly ?string $especDocto = null, // ESPECIE DO DOCUMENTO
        public readonly ?string $especMoeda = null, // ESPECIE DA MOEDA
        public readonly ?string $exibeLinDig = null, // EXIBE LINHA DIGITAVEL
        public readonly ?int $horaImpressao = null, // HORA DA IMPRESSAO (HHMMSS)
        public readonly ?float $identTitDda = null, // IDENTIFICACAOD O TITULO DDA
        public readonly ?string $indBoletoDda = null, // INDICADOR DE BOLETO DDA
        public readonly ?string $indParcelaPrin = null, // INDICADOR DE PARCELA PRINCIPAL
        public readonly ?string $indTitParceld = null, // INDICADOR DE PARCELAMENTO
        public readonly ?string $informacoesChavePix = null, // INFORMACOES SORBE POSSIVEIS FALHAS NA VALIDACAO DA CHAVE PIX
        public readonly ?string $linhaDig = null, // LINHA DIGITAVEL
        public readonly ?string $mensagem = null, // MENSAGEM RETORNO MAINFRAME
        public readonly ?string $nomeCedente = null, // NOME DO CEDENTE
        public readonly ?string $nomeSacado = null, // NOME DO SACADO
        public readonly ?string $nomeSacador = null, // NOME DO SACADOR
        public readonly ?string $nroEndCed = null, // NUMERO DO ENDERECO DO CEDENTE
        public readonly ?string $numCartor = null, // NUMERO DO CARTORIO
        public readonly ?string $numProtoc = null, // NUMERO DO PROTOCOLO
        public readonly ?int $oriProt = null, // ORIGEM DO PROTOCOLO
        public readonly ?string $permitePgtoParcial = null, // PERMITE PAGAMENTO PARCIAL
        public readonly ?float $qmoedaComisPerm = null, // VALOR DO JUROS
        public readonly ?int $qtdDiasDecurPrz = null, // QUANTIDADE DE DIAS DECURSO PRAZO
        public readonly ?int $qtdPagto = null, // QUANTIDADE DE PAGAMENTO
        public readonly ?int $qtdeCas = null, // QUANTIDADE DE CASAS
        public readonly ?int $qtdeCasDe1 = null, // QUANTIDADE DE CASAS
        public readonly ?int $qtdeCasDe2 = null, // QUANTIDADE DE CASAS
        public readonly ?int $qtdeCasDe3 = null, // QUANTIDADE DE CASAS
        public readonly ?int $qtdeCasMul = null, // QUANTIDADE DE CASAS DA MULTA
        public readonly ?float $qtdeMoeda = null, // QUANTIDADE DA MOEDA
        public readonly ?int $qtdePgtoParcial = null, // QUANTIDADE PAGAMENTOS PARCIAIS
        public readonly ?int $razCredt = null, // RAZAO DA CONTA DE CREDITO
        public readonly ?string $schavePix = null, // CHAVE PIX DO BENEFICIÁRIO
        public readonly ?string $semvQrcode = null, // CÓDIGO EMV DO QR CODE GERADO PELO PIX
        public readonly ?string $sfiller = null, // IMPLEMENTAÇÕES FUTURAS
        public readonly ?string $snumero = null, // SEU NUMERO
        public readonly ?string $status = null, // DESCRICAO DA SITUACAO
        public readonly ?string $tipEndo = null, // TIPO ENDOSSANTE
        public readonly ?string $ufCedente = null, // ESTADO DO CEDENTE
        public readonly ?string $ufSacado = null, // ESTADO DO SACADO
        public readonly ?string $ufSacador = null, // ESTADO DO SACADOR
        public readonly ?float $valAbat = null, // VALOR DE ABATIMENTO DO TITULO
        public readonly ?float $valDesc1 = null, // VALOR DO PRIMEIRO DESCONTO
        public readonly ?float $valDesc2 = null, // VALOR DO SEGUNDO DESCONTO
        public readonly ?float $valDesc3 = null, // VALOR DO TERCEIRO DESCONTO
        public readonly ?float $valMoeda = null, // VALOR DO TITULO
        public readonly ?float $valMulta = null, // VALOR DA MULTA
        public readonly ?float $valPerm = null, // VALOR DO JUROS
        public readonly ?float $valorIof = null, // VALOR DO IOF DO TITULO
        public readonly ?float $valorMoedaBol = null, // VALOR DO BOLETO
        public readonly ?float $vlrPagto = null, // VALOR DO PAGAMENTO
    ) {}
}
