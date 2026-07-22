<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\SaqueTroco;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de remuneração de um Saque Pix — usado tanto no analítico
 * (`GET /remuneracao-analiticos`) quanto no consolidado
 * (`GET /remuneracao-consolidados`).
 *
 * A spec (v1.0.7) não detalha o schema da resposta; os campos cobrem o que a
 * consulta retorna por conta e período. Valores monetários vêm como string.
 */
final class RemuneracaoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idConta = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $dataLancamento = null,
        public readonly ?string $pontoAtendimentoId = null,
        public readonly ?string $valorRemuneracao = null,
        public readonly ?string $valorSaque = null,
        public readonly ?string $quantidadeSaques = null,
    ) {}
}
