<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Título de cobrança em detalhe — é este bloco que traz `linhaDig` e
 * `codBarras`, ou seja, o insumo da 2ª VIA do boleto.
 *
 * Origem: POST /boleto/cobranca-consulta/v1/consultar (campo `titulo`)
 */
final class TituloDetalhado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dataEmis = null,
        public readonly ?string $especDocto = null,
        public readonly ?int $qtdPagto = null,
        public readonly ?string $exibeLinDig = null,
        public readonly ?Pessoa $sacador = null,
        public readonly ?string $dataCartor = null,
        public readonly ?string $snumero = null,
        public readonly ?string $corige35 = null,
        public readonly ?int $ctaCred = null,
        public readonly ?int $valPerm = null,
        public readonly ?int $despCart = null,
        public readonly ?string $dataLimitePgt = null,
        public readonly ?Pessoa $cedente = null,
        public readonly ?string $dataReg = null,
        public readonly ?string $dataVencto = null,
        public readonly ?string $numCartor = null,
        public readonly ?int $bcoProc = null,
        public readonly ?string $indTitParceld = null,
        public readonly ?int $qtdeCas = null,
        public readonly ?int $bcoDepos = null,
        public readonly ?string $descrDesc2 = null,
        public readonly ?string $digCred = null,
        public readonly ?string $descrDesc3 = null,
        public readonly ?int $dtPagto = null,
        public readonly ?int $bcoCentr = null,
        public readonly ?string $dataPerm = null,
        public readonly ?int $diasProt = null,
        public readonly ?string $cebp = null,
        public readonly ?string $aceite = null,
        public readonly ?int $qtdDiasDecurPrz = null,
        public readonly ?int $oriProt = null,
        public readonly ?int $codComisPerm = null,
        public readonly ?int $qtdeCasDe1 = null,
        public readonly ?int $ageCentr = null,
        public readonly ?string $dataPedSus = null,
        public readonly ?string $indBoletoDda = null,
        public readonly ?int $qtdePgtoParcial = null,
        public readonly ?int $codValMul = null,
        public readonly ?int $qtdeCasMul = null,
        public readonly ?string $permitePgtoParcial = null,
        public readonly ?int $ctpoVencto = null,
        public readonly ?string $dataVenctoBol = null,
        public readonly ?string $debitoAuto = null,
        public readonly ?int $diasJuros = null,
        public readonly ?int $diasComisPerm = null,
        public readonly ?string $dataSust = null,
        public readonly ?int $codStatus = null,
        public readonly ?int $identTitDda = null,
        public readonly ?int $cense = null,
        public readonly ?int $agenOper = null,
        public readonly ?string $status = null,
        public readonly ?int $valorIof = null,
        public readonly ?string $indParcelaPrin = null,
        public readonly ?int $ageProc = null,
        public readonly ?int $agencCred = null,
        public readonly ?int $qtdeMoeda = null,
        public readonly ?int $cip = null,
        public readonly ?string $enderecoEma = null,
        public readonly ?string $linhaDig = null,
        public readonly ?int $valorMoedaBol = null,
        public readonly ?int $codValDe1 = null,
        public readonly ?int $valMulta = null,
        public readonly ?int $codInscrProt = null,
        public readonly ?int $qtdeCasDe3 = null,
        public readonly ?int $qtdeCasDe2 = null,
        public readonly ?int $codValDe2 = null,
        public readonly ?int $agenDepos = null,
        public readonly ?int $codValDe3 = null,
        public readonly ?int $valMoeda = null,
        public readonly ?int $dataImpressao = null,
        public readonly ?int $qmoedaComisPerm = null,
        public readonly ?int $valDesc1 = null,
        public readonly ?int $valDesc2 = null,
        public readonly ?int $valDesc3 = null,
        public readonly ?BaixaTitulo $baixa = null,
        public readonly ?string $dataDesc1 = null,
        public readonly ?int $valAbat = null,
        public readonly ?string $dataDesc2 = null,
        public readonly ?string $tipEndo = null,
        public readonly ?string $descrMoeda = null,
        public readonly ?string $descrDesc1 = null,
        public readonly ?int $razCredt = null,
        public readonly ?string $descrMulta = null,
        public readonly ?string $dataDesc3 = null,
        public readonly ?int $acessEsc = null,
        public readonly ?int $horaImpressao = null,
        public readonly ?string $especMoeda = null,
        public readonly ?Pessoa $sacado = null,
        public readonly ?string $descrEspec = null,
        public readonly ?string $numProtoc = null,
        public readonly ?string $ctrlPartic = null,
        public readonly ?string $dataInstr = null,
        public readonly ?float $vlrPagto = null,
        public readonly ?string $dataMulta = null,
        public readonly ?string $codBarras = null,
        public readonly ?int $diasMulta = null,
    ) {}
}
