<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Comprovante DETALHADO (2ª via) do pagamento de veículo 0 km em SP,
 * recuperado pela `chavePagamento`.
 *
 * Origem: POST /v1/debitos-veiculares-sp/primeiro-veiculo/consulta-comprovante/listarComprovanteDetalhadoVeiculoZeroKm
 */
final class SpZeroKmComprovanteDetalhadoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $valor1Veiculo = null,  // ex.: 432.49
        public readonly ?float $tarifa = null,  // ex.: 10.01
        #[ArrayOf(SpMensagemComprovanteItem::class)] public readonly array $listaMsgs = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?string $dataPagamento = null,  // ex.: "05.12.2024"
        public readonly ?int $codigoReceita = null,  // ex.: 4005
        public readonly ?int $nsuProdesp = null,  // ex.: 10000005
        public readonly ?int $codigoServico = null,  // ex.: 6
        public readonly ?string $horaPagamento = null,  // ex.: "10:09:55"
        public readonly ?string $dataContabil = null,  // ex.: "05.12.2024"
        public readonly ?int $codigoCanal = null,  // ex.: 14
        public readonly ?string $autenticacaoDigital = null,
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $nsuBanco = null,  // ex.: 0
        public readonly ?float $taxaTransferencia = null,  // ex.: 10.01
        public readonly ?string $codigoPrograma = null,
        public readonly ?int $quantidadeMensagens = null,  // ex.: 1
        public readonly ?int $numeroDocumento = null,  // ex.: 1620000
        public readonly ?string $codigoLocal = null,
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?string $tipoConta = null,  // ex.: "P"
    ) {}
}
