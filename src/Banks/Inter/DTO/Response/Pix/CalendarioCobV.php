<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Os campos aninhados sob o identificador calendário organizam informações a
 * respeito de controle de tempo da cobrança.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CalendarioCobV`).
 */
final class CalendarioCobV implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $criacao = null,
        /** Trata-se de uma data, no formato `YYYY-MM-DD`, segundo ISO 8601. É a data de vencimento da cobrança. A cobrança pode ser honrada até esse dia, inclusi… */
        public readonly ?string $dataDeVencimento = null,
        /** Trata-se da quantidade de dias corridos após calendario.dataDeVencimento, em que a cobrança poderá ser paga. Aplica-se este campo sobre o vencimento o… */
        public readonly ?int $validadeAposVencimento = null,
    ) {}
}
