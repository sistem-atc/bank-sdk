<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Título liquidado (pago) no período consultado.
 * Origem: POST /boleto/cobranca-lista/v1/listar (item de `titulos`)
 */
final class TituloLiquidado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $bancoRecebedor = null,
        public readonly ?int $agenciaRecebedora = null,
        public readonly ?int $nossoNumero = null,
        public readonly ?string $digitoNossoNumero = null,
        public readonly ?string $tipoRegistro = null,
        public readonly ?string $seuNumero = null,
        public readonly ?string $dataVencimento = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $dataMovimento = null,
        public readonly ?string $nomePagador = null,
        public readonly ?string $descricaoOrigemPagamento = null,
        public readonly ?int $valorTitulo = null,
        public readonly ?int $valorPagamento = null,
        public readonly ?int $valorOscilacao = null,
        public readonly ?string $sinalValorOscilacao = null,
        public readonly ?int $numeroSequenciaTitulo = null,
        public readonly ?int $numeroSequenciaPagamento = null,
        public readonly ?int $codigoFormaCredito = null,
        public readonly ?string $descricaoFormaCredito = null,
        public readonly ?string $indicadorPagoCartorio = null,
    ) {}
}
