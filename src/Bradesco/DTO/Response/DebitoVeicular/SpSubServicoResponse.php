<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Sub-serviços de um serviço do DETRAN-SP, já com o valor da taxa, a tarifa
 * de postagem e o `codigoReceita` que o pagamento exige.
 *
 * Origem: POST /v1/debitos-veiculares-sp/taxas/lista-subservicos/listaTipoSubServicoSP
 */
final class SpSubServicoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SpSubServicoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?string $codigoPrograma = null,
        public readonly ?int $codigoServico = null,  // ex.: 2
        public readonly ?string $codigoLocal = null,
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
        public readonly ?int $nsuBanco = null,  // ex.: 208
    ) {}
}
