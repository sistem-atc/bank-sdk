<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Débitos do primeiro licenciamento de veículo 0 km em SP (consulta por
 * CPF/CNPJ do adquirente, não por RENAVAM — o veículo ainda não tem um).
 *
 * Origem: POST /v1/debitos-veiculares-sp/primeiro-veiculo/lista-debitos/consultarDebitosVeicularesSP
 */
final class SpZeroKmDebitosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0001"
        public readonly ?float $valorTaxaLicenciamento = null,  // ex.: 452.79
        public readonly ?string $dataContabil = null,  // ex.: "07.04.2025"
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $nsuBanco = null,  // ex.: 234
        public readonly ?float $valorTaxaTransferencia = null,  // ex.: 0
        public readonly ?string $descricaoTributo = null,  // ex.: "LIC VEICULOS NOVOS/1. REG"
        public readonly ?float $valorTarifaBancaria = null,  // ex.: 0
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 402186670
        public readonly ?int $codigoTributo = null,  // ex.: 62
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 19
        public readonly ?string $descricaoMensagem = null,  // ex.: "CONSULTA EFETUADA COM SUCESSO"
    ) {}
}
