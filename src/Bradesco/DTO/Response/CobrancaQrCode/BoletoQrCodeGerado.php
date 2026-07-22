<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Boleto híbrido (Bolecode) recém-registrado — resposta do registro de boleto
 * com QR Code.
 *
 * Endpoint: POST /boleto-hibrido/cobranca-registro/v1/gerarBoleto
 *
 * Os campos com sufixo `10` vêm assim do mainframe do Bradesco (layout de
 * registro); mantidos com o nome original da spec.
 */
final class BoletoQrCodeGerado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cidtfdProdCobr = null, // Identificador do Produto Cobrança (Carteira).
        public readonly ?float $cnegocCobr = null, // Número do Contrato (Negociação Agência + Conta).
        public readonly ?float $cpssoaJuridContr = null, // Código da pessoa jurídica do contrato.
        public readonly ?int $ctpoContrNegoc = null, // Tipo do Contrato.
        public readonly ?float $nseqContrNegoc = null, // Número do Contrato.
        public readonly ?int $cprodtServcOper = null, // Código do Produto Cobrança.
        public readonly ?float $ctitloCobrCdent = null, // Nosso Número.
        public readonly ?int $tp08Reg1 = null, // Tipo de registro 1 - Dados do Sacado / Beneficiário.
        public readonly ?int $agencCred10 = null, // Número da Agência de crédito do beneficiário.
        public readonly ?float $ctaCred10 = null, // Número da conta de crédito do beneficiário.
        public readonly ?string $digCred10 = null, // Dígito verificador da conta de crédito do beneficiário.
        public readonly ?int $cip10 = null, // Código da cartela de instrução permanente.
        public readonly ?int $codStatus10 = null, // Status do título | Ex. 01.
        public readonly ?string $status10 = null, // Descrição do status do título | Ex. A Vencer / Vencido.
        public readonly ?string $cedente10 = null, // Descrição do Campo Nome do Cedente | Ex. Nome do Cedente.
        public readonly ?string $endCedente10 = null, // Descrição do endereço do Cedente | Ex. Endereço Cedente.
        public readonly ?string $nroEndCed10 = null, // Número do logradouro do beneficiário (Cedente).
        public readonly ?string $comEndCed10 = null, // Complemento do logradouro do beneficiário (Cedente).
        public readonly ?string $baiCedente10 = null, // Descrição do Campo Bairro do Cedente | Ex. Bairro Cedente.
        public readonly ?int $cepEndCed10 = null, // CEP do beneficiário (Cedente).
        public readonly ?int $cepcCedente10 = null, // Complemento do CEP do beneficiário (Cedente).
        public readonly ?string $cidCedente10 = null, // Município do beneficiário (Cedente).
        public readonly ?string $ufCedente10 = null, // Sigla da Unidade Federativa do beneficiário (Cedente).
        public readonly ?int $razCredt10 = null, // Razão de crédito .
        public readonly ?string $nomeSacado10 = null, // Nome do devedor (Sacado).
        public readonly ?float $cnpjSacado10 = null, // CPF ou CNPJ do devedor (Sacado).
        public readonly ?string $endSacado10 = null, // Endereço do devedor (Sacado).
        public readonly ?string $baiSacado10 = null, // Bairro do logradouro do devedor (Sacado).
        public readonly ?string $cidSacado10 = null, // Município do devedor (Sacado) .
        public readonly ?string $ufSacado10 = null, // Sigla da Unidade Federativa do devedor (Sacado).
        public readonly ?int $cepSacado10 = null, // CEP do devedor (Sacado).
        public readonly ?string $cepcSacado10 = null, // Complemento do CEP do devedor (Sacado).
        public readonly ?string $cebp10 = null, // Identificador de rateio de crédito. S = Sim | N = Não.
        public readonly ?string $debitoAuto10 = null, // Identificador de débito automático.
        public readonly ?string $aceite10 = null, // Identificador de aceite do devedor (Sacado). S = Sim | N = Não.
        public readonly ?string $enderecoEma10 = null, // Endereço eletrônico do devedor - e-mail (Sacado).
        public readonly ?string $nomeSacador10 = null, // Nome do Sacador Avalista.
        public readonly ?float $cnpjSacador10 = null, // CPF ou CNPJ do Sacador Avalista.
        public readonly ?string $endSacador10 = null, // Endereço do Sacador Avalista.
        public readonly ?string $cidSacador10 = null, // Município do Sacador Avalista.
        public readonly ?string $ufSacador10 = null, // Sigla da Unidade Federativa do Sacador Avalista.
        public readonly ?int $cepSacador10 = null, // CEP do Sacador Avalista.
        public readonly ?int $cepcSacador10 = null, // Complemento do CEP do Sacador Avalista.
        public readonly ?string $sfiller6 = null, // Implementações futuras.
        public readonly ?int $tp08Reg2 = null, // Tipo de registro 2 - Dados do título pendente.
        public readonly ?int $cense10 = null, // Identificador do subcentro.
        public readonly ?int $agenOper10 = null, // Número da Agência operadora.
        public readonly ?int $bcoDepos10 = null, // Código do Banco depositário.
        public readonly ?int $agenDepos10 = null, // Número da Agência depositária.
        public readonly ?string $snumero10 = null, // Seu Número.
        public readonly ?string $dataReg10 = null, // Data de registro do título.
        public readonly ?string $especDocto10 = null, // Sigla da espécie do título.
        public readonly ?string $descrEspec10 = null, // Descrição da espécie do título.
        public readonly ?float $valorIof10 = null, // Valor do IOF.
        public readonly ?string $dataEmis10 = null, // Data de emissão do título (DD.MM.AAAA).
        public readonly ?string $especMoeda10 = null, // Sigla da moeda do título. Ex. R$.
        public readonly ?float $qtdeMoeda10 = null, // Quantidade de moeda do título.
        public readonly ?int $qtdeCas10 = null, // Quantidade de casas decimais da moeda do título.
        public readonly ?string $dataVencto10 = null, // Data de vencimento do título (DD.MM.AAAA).
        public readonly ?string $descrMoeda10 = null, // Sigla do indicador econômico (moeda). Ex. R$.
        public readonly ?float $valMoeda10 = null, // Valor nominal do título.
        public readonly ?float $valAbat10 = null, // Valor do abatimento do título.
        public readonly ?string $dataInstr10 = null, // Data de instrução de protesto.
        public readonly ?int $diasProt10 = null, // Quantidade de dias para protesto automático.
        public readonly ?string $dataCartor10 = null, // Data de envio ao cartório.
        public readonly ?string $numCartor10 = null, // Número do cartório de protesto.
        public readonly ?string $numProtoc10 = null, // Número do protocolo de protesto.
        public readonly ?string $dataPedSus10 = null, // Data da solicitação de sustação do protesto.
        public readonly ?string $dataSust10 = null, // Data da efetivação da sustação de protesto.
        public readonly ?string $dataMulta10 = null, // Data da multa após vencimento.
        public readonly ?float $valMulta10 = null, // Valor da multa.
        public readonly ?int $qtdeCasMul10 = null, // Quantidade de casas decimais do valor da multa.
        public readonly ?int $codValMul10 = null, // Código do valor da multa. 1 = Valor | 2 = Percentual.
        public readonly ?string $descrMulta10 = null, // Descrição da multa. Ex. Valor Fixo ou Taxa Mensal ou Isento.
        public readonly ?string $dataPerm10 = null, // Data da comissão de permanência após vencimento (juros).
        public readonly ?float $valPerm10 = null, // Valor da comissão de permanência após vencimento (juros).
        public readonly ?string $dataDesc110 = null, // Data-limite do primeiro desconto.
        public readonly ?float $valDesc110 = null, // Valor do primeiro desconto.
        public readonly ?int $qtdeCasDe110 = null, // Quantidade de casas decimais do valor do primeiro desconto.
        public readonly ?int $codValDe110 = null, // Código do valor do primeiro desconto. 1 = Valor | 2 = Percentual.
        public readonly ?string $descrDesc110 = null, // Descrição do primeiro desconto. Ex. Valor Fixo por Antecipação até a Data | Percentual por Antecipação até a D
        public readonly ?string $dataDesc210 = null, // Data-limite do segundo desconto.
        public readonly ?float $valDesc210 = null, // Valor do segundo desconto.
        public readonly ?int $qtdeCasDe210 = null, // Quantidade de casas decimais do valor do segundo desconto.
        public readonly ?int $codValDe210 = null, // Código do valor do segundo desconto. 1 = Valor | 2 = Percentual.
        public readonly ?string $descrDesc210 = null, // Descrição do segundo desconto. Ex. Valor Fixo por Antecipação até a Data | Percentual por Antecipação até a Da
        public readonly ?string $dataDesc310 = null, // Data-limite do terceiro desconto.
        public readonly ?float $valDesc310 = null, // Valor do terceiro desconto .
        public readonly ?int $qtdeCasDe310 = null, // Quantidade de casas decimais do valor do terceiro desconto.
        public readonly ?int $codValDe310 = null, // Código do valor do terceiro desconto. 1 = Valor | 2 = Percentual.
        public readonly ?string $descrDesc310 = null, // Descrição do terceiro desconto. Ex. Valor Fixo por Antecipação até a Data | Percentual por Antecipação até a D
        public readonly ?int $diasMulta10 = null, // Quantidade de dias após o vencimento, para início da incidência de multa.
        public readonly ?int $diasJuros10 = null, // Quantidade de dias após o vencimento, para início da incidência de juros.
        public readonly ?string $codBarras10 = null, // Código de barras do título .
        public readonly ?string $linhaDig10 = null, // Linha digitável do título.
        public readonly ?float $despCart10 = null, // Valor das despesas cartorárias.
        public readonly ?int $bcoCentr10 = null, // Código do Banco de protesto.
        public readonly ?int $ageCentr10 = null, // Número da Agência de protesto.
        public readonly ?float $acessEsc10 = null, // Número do acessório escritural.
        public readonly ?string $tipEndo10 = null, // Tipo de endosso do título.
        public readonly ?int $oriProt10 = null, // Origem do protesto.
        public readonly ?string $corige3510 = null, // Código de origem.
        public readonly ?int $ctpoVencto10 = null, // Tipo de vencimento.
        public readonly ?int $codInscrProt10 = null, // Código da instrução de protesto do título. 1 = Dias corridos | 2 = Dias úteis.
        public readonly ?int $codDecurPrz10 = null, // Código para decurso de prazo. 1 = Dias corridos | 2 = Dias úteis.
        public readonly ?int $qtdDDecurPrz10 = null, // Quantidade de dias para decurso de prazo do título após o vencimento.
        public readonly ?int $ctpoAbat10 = null, // Tipo de abatimento.
        public readonly ?int $codComisPerm10 = null, // Código da comissão de permanência (juros).
        public readonly ?int $ctpoDesc110 = null, // Tipo do primeiro desconto.
        public readonly ?int $ctpoDesc210 = null, // Tipo do segundo desconto.
        public readonly ?int $ctpoDesc310 = null, // Tipo do terceiro desconto.
        public readonly ?string $ctrlPartic10 = null, // Controle do participante.
        public readonly ?int $diasComisPerm10 = null, // Quantidade de dias para incidência da comissão de permanência após vencimento (juros).
        public readonly ?int $codComisPerm101 = null, // Código da comissão de permanência (juros).
        public readonly ?float $qmoedaComisPerm = null, // Quantidade de moeda da comissão de permanência (juros).
        public readonly ?string $cnpjCpfCedente10 = null, // CPF ou CNPJ do beneficiário (Cedente).
        public readonly ?float $valorMoedaBol10 = null, // Valor do boleto em moeda vigente.
        public readonly ?string $dataVenctoBol10 = null, // Data de vencimento do título (DD.MM.AAAA).
        public readonly ?string $indTitParceld10 = null, // Indicador de título parcelado.
        public readonly ?string $indParcelaPrin10 = null, // Indicador da primeira parcela .
        public readonly ?string $indBoletoDda10 = null, // Indicador de boleto DDA. S = Sim | N = Não.
        public readonly ?string $dataLimitePgt10 = null, // Data-limite para pagamento do título.
        public readonly ?int $dataImpressao10 = null, // Data da impressão do boleto.
        public readonly ?int $horaImpressao10 = null, // Hora da impressão do boleto.
        public readonly ?float $identTitDda10 = null, // Identificação do título DDA.
        public readonly ?string $exibeLinDig10 = null, // Indicador de exibição da linha digitável. S = Sim | N = Não.
        public readonly ?string $permPgtoParcial = null, // Indicador de pagamento parcial. S = Sim | N = Não.
        public readonly ?int $qtdePgtoParcial = null, // Quantidade de pagamento parcial.
        public readonly ?string $filler5 = null, // Implementações futuras.
        public readonly ?int $sFase = null, // Fase de atualização do QR Code. 1 = Registro do título e envio ao BSPI | 2 = Vinculação do título com QR Code.
        public readonly ?string $cindcdCobrMisto = null, // Indicador do registro de título com QR Code. S = Sim | N = Não.
        public readonly ?string $ialiasAdsaoCta = null, // Chave Pix do beneficiário.
        public readonly ?string $iconcPgtoSpi = null, // TXID do título.
        public readonly ?string $caliasAdsaoCta = null, // Códigos de erro na geração do QR Code pelo BSPI.
        public readonly ?string $ilinkGeracQrcd = null, // Identificação do location do QR Code gerado pelo BSPI.
        public readonly ?string $wqrcdPdraoMercd = null, // Código EMV do QR Code gerado pelo BSPI.
        public readonly ?string $sInfoAdicNome = null, // Consultar descricao no doc auxiliar.
        public readonly ?string $sInfoAdicCpfCnpj = null, // Consultar descricao no doc auxiliar.
        public readonly ?string $validadeAposVencimento = null, // Quantidade de dias após o vencimento, que o título é válido para pagamento via Pix.
        public readonly ?string $qFiller6 = null, // Implementações futuras.
    ) {}
}
