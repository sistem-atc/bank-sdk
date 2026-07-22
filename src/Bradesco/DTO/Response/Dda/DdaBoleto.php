<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Dda;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Um boleto DDA (Débito Direto Autorizado) devolvido pela consulta Bradesco —
 * um título registrado CONTRA o CNPJ da empresa, candidato a virar conta a
 * pagar no host.
 *
 * PARCIAL POR DESIGN: os campos exatos e a nomenclatura dependem da API DDA
 * contratada; todo campo é nullable e o AutoHydrate ignora chave ausente.
 * Os nomes seguem o vocabulário FEBRABAN (linha digitável, sacador/avalista,
 * valor nominal) e o mapa `#[JsonKey]` deve ser ajustado quando o payload real
 * do Bradesco estiver em mãos.
 */
final class DdaBoleto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $linhaDigitavel = null,
        public readonly ?string $codigoBarras = null,
        public readonly ?string $nomeBeneficiario = null,
        public readonly ?string $documentoBeneficiario = null,
        public readonly ?string $nomePagador = null,
        public readonly ?string $documentoPagador = null,
        public readonly ?float $valorNominal = null,
        public readonly ?string $dataVencimento = null,
        public readonly ?string $situacao = null,
    ) {}
}
