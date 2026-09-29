<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Attributes;

use Attribute;

/**
 * Valor monetário/percentual: serializa como string decimal com 2 casas
 * ("39.50", "10.00"), o formato que a PagBrasil espera — e que a API v2 do
 * PagStream EXIGE (número JSON é recusado em vários campos).
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Money
{
}
