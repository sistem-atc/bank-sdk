<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resultado por linha de rateio após a manutenção do split payment — traz a
 * ação solicitada e o status/motivo com que o Bradesco a processou.
 * Origem: POST /boleto/cobranca-manutencao-split/v1/manutencao-rateio-credito
 */
final class RateioCreditoResultado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Determina a ação a ser efetuada no processamento. I = Inclusão, A = Alteração, E = Exclusão */
        public readonly ?string $acaoRteio = null,
        /** Agência para crédito do rateio ao beneficiário */
        public readonly ?int $cagBnefcRteio = null,
        /** Conta para crédito do rateio ao beneficiário */
        public readonly ?int $cctaBnefcRteio = null,
        /** Valor ou percentual do rateio para o beneficiário */
        public readonly ?string $vlrPercRteio = null,
        /** Nome do beneficiário do rateio */
        public readonly ?string $ibnefcRteioCredt = null,
        /** Para diferenciar rateios de um mesmo título para o mesmo beneficiário várias vezes. */
        public readonly ?string $pcelaRteioCredt = null,
        /** Informar a quantidade de dias para rateio, após a data do crédito da cobrança na conta-corrente do beneficiário. Essa quantidade está limitada a 30 (trinta) dias. */
        public readonly ?int $floatRteioBnefc = null,
        /** Indicador do resultado da execução para a ação informada. */
        public readonly ?string $statusAcaoRteio = null,
        /** Descrição da causa do resultado da execução. */
        public readonly ?string $rmotvoStatusAcao = null,
    ) {}
}
