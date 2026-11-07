<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * É uma estrutura opcional relacionada ao conceito de recebimento de
 * numerário. Apenas um agrupamento por vez é permitido, quando há `saque` não
 * há `troco` e vice-versa. Quando uma cobrança imediata tem uma estrutura de
 * `retirada` ela deixa de ser considerada Pix comum e passa à categoria de Pix
 * Saque ou Pix Troco. Para que o preenchimento do objeto `retirada` seja
 * considerado válido as seguintes regras se aplicam: - os campos
 * `modalidadeAgente` e `prestadorDoServicoDeSaque` são de **preenchimento
 * obrigatório**; - quando o `saque` estiver presente a cobrança deve respeitar
 * as seguintes condições
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Retirada`).
 */
final class Retirada implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Informações relacionadas ao saque */
        public readonly ?Saque $saque = null,
        /** Informações relacionadas ao troco */
        public readonly ?Saque $troco = null,
    ) {}
}
