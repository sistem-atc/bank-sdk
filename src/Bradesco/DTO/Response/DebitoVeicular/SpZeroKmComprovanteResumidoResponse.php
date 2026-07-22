<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista resumida dos comprovantes de veículo 0 km em SP, por CPF/CNPJ e
 * período.
 *
 * Origem: POST /v1/debitos-veiculares-sp/primeiro-veiculo/lista-comprovantes/listaComprovanteVeicResSp
 */
final class SpZeroKmComprovanteResumidoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        #[ArrayOf(SpZeroKmComprovanteItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?string $codigoPrograma = null,
        public readonly ?int $codigoCanal = null,  // ex.: 14
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 440010178
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 90
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
