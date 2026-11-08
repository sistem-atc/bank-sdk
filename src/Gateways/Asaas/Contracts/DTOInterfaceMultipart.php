<?php

namespace SistemAtc\Banks\Gateways\Asaas\Contracts;

interface DTOInterfaceMultipart
{
    public function toMultipart(): array;
}