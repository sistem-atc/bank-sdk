<?php

namespace SistemAtc\Banks\Gateways\Asaas\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayOf
{
    public function __construct(
        public string $class
    ) {}
}