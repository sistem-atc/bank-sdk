<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `data[]` da consulta de detalhe do boleto — `GET /boletoscash/v2/
 * boletos` (produto "Boletos Cobrança - Consulta de detalhe do Boleto").
 */
final class BoletoDetalhe implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idBoleto = null,
        public readonly ?Beneficiario $beneficiario = null,
        public readonly ?DadoBoleto $dadoBoleto = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $acoesPermitidas = null,
    ) {}
}
