<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Título pendente de liquidação (ainda em aberto na carteira).
 * Origem: POST /boleto/cobranca-pendente/v1/listar (item de `titulos`)
 */
final class TituloPendente implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $codStatus = null,
        public readonly ?string $descrStatus = null,
        public readonly ?PessoaResumo $pagador = null,
        public readonly ?string $debitoAuto = null,
        public readonly ?string $aceite = null,
        public readonly ?string $rateio = null,
        public readonly ?PessoaResumo $sacador = null,
        public readonly ?int $bcoDepos = null,
        public readonly ?int $agenDepos = null,
        public readonly ?int $nossoNumero = null,
        public readonly ?string $seuNumero = null,
        public readonly ?string $especDocto = null,
        public readonly ?string $dataReg = null,
        public readonly ?string $dataEmis = null,
        public readonly ?string $dataVencto = null,
        public readonly ?int $qtdeDecima = null,
        public readonly ?int $valTitulo = null,
        public readonly ?string $ctrlPartic = null,
        public readonly ?string $indTitParceld = null,
        public readonly ?string $indParcelaPrin = null,
        public readonly ?string $indBoletoDda = null,
        public readonly ?string $indBolQrcode = null,
    ) {}
}
