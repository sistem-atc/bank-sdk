<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\BoletoNegociado;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Um ativo financeiro vinculado a boletos (`GET /boleto/v1/ativos-financeiros`)
 * — usado na negociação/cessão de recebíveis (antecipação).
 *
 * `pessoaTitular` e `boleto` ficam como array cru (schemas profundos).
 */
final class AtivoFinanceiro implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<string, mixed>  $pessoaTitular
     * @param  array<string, mixed>  $boleto
     */
    public function __construct(
        public readonly ?string $numeroIdentificacaoAtivoFinanceiro = null,
        public readonly ?string $tipoAtivoFinanceiro = null,
        public readonly ?string $tipoNegociacao = null,
        public readonly ?string $codigoEscrituradora = null,
        public readonly ?string $dataHoraSituacao = null,
        public readonly array $pessoaTitular = [],
        public readonly array $boleto = [],
    ) {}
}
