<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Tipos de débito/tributo aceitos pelo DETRAN-SP (tabela de `codigoTributo`).
 *
 * Origem: POST /v1/debitos-veiculares-sp/renavam/lista-tipo-debitos/listaTipoPagamentoTxSP
 */
final class SpTipoDebitoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SpTipoDebitoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
