<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Serviços do DETRAN-SP passíveis de cobrança de taxa (tabela de
 * `codigoServico`).
 *
 * Origem: POST /v1/debitos-veiculares-sp/taxas/lista-servicos/consulta/servico
 */
final class SpServicoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SpServicoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
