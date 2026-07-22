<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Comprovante DETALHADO (2ª via) de um pagamento de débito veicular de MG.
 *
 * Origem: POST /v1/debitos-veiculares-mg/lista-comprovante-detalhada/consultaComprovanteMG
 */
final class MgComprovanteDetalhadoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeOrgao = null,  // ex.: "SECRETARIA DE ESTADO DE FAZENDA DE MINAS GERAIS"
        public readonly ?string $localidadeVeiculo = null,  // ex.: "BELO HORIZONTE - MG"
        public readonly ?string $descricaoCanal = null,  // ex.: "API Corp Server to Server"
        public readonly ?string $descricaoCpfCnpj = null,  // ex.: "CPF"
        public readonly ?string $tipoProprietario = null,  // ex.: "CUST"
        public readonly ?int $codigoRenavam = null,  // ex.: 246304715
        public readonly ?string $cpfCnpjCifrado = null,  // ex.: "***.570.816-**"
        public readonly ?string $codigoUf = null,  // ex.: "MG"
        public readonly ?int $digitoConta = null,  // ex.: 2
        public readonly ?float $valorTotal = null,  // ex.: 205.28
        public readonly ?int $codigoConta = null,  // ex.: 799906
        public readonly ?float $valorMulta = null,  // ex.: 23.82
        public readonly ?string $codigoBarras = null,  // ex.: "85670000002052800632025052199002463047150211"
        public readonly ?int $autenticacaoBancaria = null,  // ex.: 1164
        public readonly ?string $codigoChassi = null,  // ex.: "9BWZZZ26ZJP012732"
        public readonly ?string $codigoMensagem = null,  // ex.: "LCBR0000"
        public readonly ?string $dataPagamento = null,  // ex.: "21/05/2025"
        public readonly ?int $identificadorDebito = null,  // ex.: 2505210000004476050
        public readonly ?string $horaPagamento = null,  // ex.: "16.20.55"
        public readonly ?float $valorPago = null,  // ex.: 205.28
        public readonly ?int $codigoCanal = null,  // ex.: 536
        public readonly ?float $valorJuros = null,  // ex.: 62.36
        public readonly ?string $nomeProprietario = null,  // ex.: "CARLOS ALBERTO SILVA DOS SANTOS"
        public readonly ?float $valorDebito = null,  // ex.: 119.1
        public readonly ?string $codigoPlaca = null,  // ex.: "GLD1623"
        public readonly ?string $descricaoTributo = null,  // ex.: "IPVA 2021-PARCELA 1"
        public readonly ?string $dataVencimento = null,  // ex.: "19.01.2021"
        public readonly ?string $tipoTributo = null,  // ex.: "IPVA"
        public readonly ?string $indicadorExibicao = null,  // ex.: "S"
        public readonly ?string $cpfCnpjProprietario = null,  // ex.: "83957081653"
        public readonly ?float $valorBase = null,  // ex.: 119.1
        public readonly ?string $codigoMunicipio = null,  // ex.: "BELO HORIZONTE"
        public readonly ?string $descricaoMensagem = null,  // ex.: "Operação executada com sucesso."
        public readonly ?int $codigoAgencia = null,  // ex.: 3929
        public readonly ?string $tipoConta = null,  // ex.: "C"
    ) {}
}
