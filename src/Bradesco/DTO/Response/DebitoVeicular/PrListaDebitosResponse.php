<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Débitos veiculares pendentes do DETRAN/SEFAZ-PR de um RENAVAM.
 *
 * Origem: POST /v1/debitos-veiculares-pr/lista-debitos/listaDebitoVeicularPR
 */
final class PrListaDebitosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(PrDebitoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?int $codigoRenavam = null,  // ex.: 323030939
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?string $devedorPrincipal = null,  // ex.: "SANDRA REGINA SOUZA CARDOSO"
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?string $codigoUf = null,  // ex.: "PR"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
        public readonly ?int $nsuBanco = null,  // ex.: 28903
        public readonly ?string $codigoPlaca = null,  // ex.: "HOC1377"
    ) {}
}
