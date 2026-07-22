<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista resumida de comprovantes de pagamento por RENAVAM em SP. A
 * `chavePagamento` de cada item é o que identifica o pagamento na consulta
 * detalhada.
 *
 * Origem: POST /v1/debitos-veiculares-sp/renavam/lista-comprovantes/listaComprovanteResSp
 */
final class SpComprovanteResumidoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 1
        public readonly ?string $tipoVeiculo = null,  // ex.: "0"
        #[ArrayOf(SpComprovanteResumidoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?int $codigoRenavam = null,  // ex.: 281985839
        public readonly ?string $nomeProprietario = null,  // ex.: "RAIMUNDO STOS C"
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $codigoUf = null,  // ex.: "SP"
        public readonly ?string $codigoPlaca = null,  // ex.: "DPC4664"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 2184366
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 93
        public readonly ?string $codigoMunicipio = null,  // ex.: "06350"
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
