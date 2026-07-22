<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Débitos veiculares do DETRAN-BA. DTO compartilhado pelas três listagens
 * (por RENAVAM, por ANO de exercício e por MULTA) — os três endpoints
 * devolvem exatamente o mesmo envelope.
 *
 * Origem: POST /v1/debitos-veiculares-ba/detran/lista-debitos/{renavan|ano|multa}
 */
final class BaListaDebitosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        public readonly ?int $codigoRenavam = null,  // ex.: 110172930
        public readonly ?int $numeroMulta = null,  // ex.: 0
        public readonly ?int $localEntrega = null,  // ex.: 2
        public readonly ?string $codigoUf = null,  // ex.: "BA"
        public readonly ?string $sequencialPeriferico = null,
        public readonly ?int $anoCrlv = null,  // ex.: 0
        public readonly ?int $nsuOrigem = null,  // ex.: 26480
        public readonly ?float $valorTotal = null,  // ex.: 173.4
        public readonly ?string $codigoLocal = null,
        public readonly ?int $numeroParcela = null,  // ex.: 0
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 4
        #[ArrayOf(BaTributoItem::class)] public readonly array $lista = [],
        public readonly ?float $valorDespesaOperacional = null,  // ex.: 0
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?int $nsuProdeb = null,  // ex.: 0
        public readonly ?float $valorTaxaLicenciamento = null,  // ex.: 173.4
        public readonly ?float $valorTarifaPostagem = null,  // ex.: 0
        public readonly ?float $valorIpva = null,  // ex.: 0
        public readonly ?string $nomeProprietario = null,  // ex.: "ABDON PEREIRA DE FRANCA NETO"
        public readonly ?float $valorTotalMulta = null,  // ex.: 0
        public readonly ?string $caracteristicaOperLynx = null,
        public readonly ?int $anoExercicio = null,  // ex.: 0
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $agenciaDestino = null,  // ex.: 145
        public readonly ?int $nsuBanco = null,  // ex.: 26480
        public readonly ?string $codigoPlaca = null,  // ex.: "JRQ0135"
        public readonly ?float $valorDpvat = null,  // ex.: 0
        public readonly ?float $valorTarifaBancaria = null,  // ex.: 0
        public readonly ?int $codigoPagamento = null,  // ex.: 401
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $nomeCliente = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 565776725
        public readonly ?int $codigoMunicipio = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?string $nomeMunicipio = null,  // ex.: "GANDU"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
