<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Attributes;

use Attribute;

/** Campo que o próprio DTO traduz em fixedFields() — o serializador pula. */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Ignore
{
}
