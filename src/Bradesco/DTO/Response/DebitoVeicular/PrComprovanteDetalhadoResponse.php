<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Comprovante DETALHADO de um pagamento de débito veicular do PR (2ª via).
 *
 * Origem: POST /v1/debitos-veiculares-pr/lista-comprovante-detalhada/consultar
 */
final class PrComprovanteDetalhadoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeTributo = null,  // ex.: "IPVA"
        public readonly ?int $codigoRenavam = null,  // ex.: 202122239
        public readonly ?float $valorTributo = null,  // ex.: 300.0
        public readonly ?string $digitoConta = null,  // ex.: "9"
        public readonly ?int $codigoSefaz = null,  // ex.: 8460
        public readonly ?int $codigoBanco = null,  // ex.: 0
        public readonly ?int $codigoConta = null,  // ex.: 999
        public readonly ?string $codigoDeBarras = null,  // ex.: "858200000031000002322021304141756480720846148705"
        public readonly ?string $codigoLocal = null,
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0001"
        public readonly ?string $dataPagamento = null,  // ex.: "13/05/2025"
        public readonly ?int $codigoAutenticacaoBancaria = null,  // ex.: 45560547
        public readonly ?string $horaPagamento = null,  // ex.: "10:59:16"
        public readonly ?string $nomeProprietario = null,  // ex.: "ADENILSON DOS SANTOS FERREIRA"
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $codigoUF = null,  // ex.: "PR"
        public readonly ?string $codigoPlaca = null,  // ex.: "CGG2700"
        public readonly ?string $descricaoTributo = null,  // ex.: "14/04/2022"
        public readonly ?int $anoExercicioTributo = null,  // ex.: 2022
        public readonly ?string $dataVencimento = null,  // ex.: "14/04/2022"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $nomeCliente = null,  // ex.: "AGLINAILSON"
        public readonly ?int $codigoTributo = null,  // ex.: 452
        public readonly ?string $descricaoMensagem = null,  // ex.: "CONSULTA EFETUADA COM SUCESSO"
        public readonly ?int $codigoAgencia = null,  // ex.: 145
        public readonly ?string $tipoConta = null,  // ex.: "C"
    ) {}
}
