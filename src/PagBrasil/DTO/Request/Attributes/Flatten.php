<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Attributes;

use Attribute;

/**
 * O DTO aninhado é "espalhado" no nível de cima: a API clássica é plana
 * (customer_name, address_street, cc_number…), mas agrupar em Customer,
 * Address e Card deixa o chamador sem 20 parâmetros soltos.
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Flatten
{
}
