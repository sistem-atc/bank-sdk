<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `dados_individuais_boleto` do Bolecode Pix. Na entrada carrega nosso
 * número, vencimento e valor; no body de SAÍDA a API acrescenta os
 * identificadores gerados do título: `id_boleto_individual`, `codigo_barras` e
 * `numero_linha_digitavel`.
 *
 * Valores monetários vêm como string de 15 inteiros + 2 decimais → `?string`.
 */
final class DadosIndividuaisBoleto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $numeroNossoNumero = null,
        public readonly ?string $dataVencimento = null,
        public readonly ?string $valorTitulo = null,
        public readonly ?string $dataLimitePagamento = null,
        public readonly ?string $textoSeuNumero = null,
        public readonly ?string $textoUsoBeneficiario = null,
        public readonly ?string $idBoletoIndividual = null,
        public readonly ?string $codigoBarras = null,
        public readonly ?string $numeroLinhaDigitavel = null,
    ) {}
}
