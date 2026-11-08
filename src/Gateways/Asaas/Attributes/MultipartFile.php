<?php

namespace SistemAtc\Banks\Gateways\Asaas\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MultipartFile {
    public function __construct(
        public string $as = 'file'
    ) {}
}