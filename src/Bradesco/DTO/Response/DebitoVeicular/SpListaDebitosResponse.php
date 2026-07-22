<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Débitos veiculares (IPVA, licenciamento, DPVAT e multas) de um RENAVAM em SP.
 *
 * Origem: POST /v1/debitos-veiculares-sp/renavam/lista-debitos/listaDebitosVeicularesSP
 */
final class SpListaDebitosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        public readonly ?string $tipoVeiculo = null,  // ex.: "0"
        #[ArrayOf(SpDebitoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?string $dataPagamento = null,  // ex.: "07.04.2025"
        public readonly ?int $codigoRenavam = null,  // ex.: 1222953460
        public readonly ?string $nomeProprietario = null,  // ex.: "ROGERIO APARECI"
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $codigoUf = null,  // ex.: "SP"
        public readonly ?string $codigoPlaca = null,  // ex.: "GFA1I11"
        public readonly ?string $anoCrlv = null,  // ex.: "2024"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 295048038
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 14
        public readonly ?string $codigoMunicipio = null,  // ex.: "04157"
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
