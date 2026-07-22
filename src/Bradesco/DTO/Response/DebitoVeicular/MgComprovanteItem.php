<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `listaComprovantes` de MgListaComprovantesResponse.
 */
final class MgComprovanteItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dataPagamento = null,  // ex.: "21/05/2025"
        public readonly ?int $identificadorDebito = null,  // ex.: 2505210000004476050
        public readonly ?string $operacao = null,  // ex.: "Débito Veicular do Estado de Minas Gerais"
        public readonly ?float $valorPago = null,  // ex.: 205.28
        public readonly ?string $codigoBarras = null,  // ex.: "85670000002052800632025052199002463047150211"
        public readonly ?string $descricao = null,  // ex.: "IPVA | SEFAZ-MG"
        public readonly ?int $autenticacaoBancaria = null,  // ex.: 1164
        public readonly ?string $status = null,  // ex.: "Pago"
    ) {}
}
