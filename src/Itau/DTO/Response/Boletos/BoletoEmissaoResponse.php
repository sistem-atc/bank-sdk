<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta da emissão/registro de boleto — `POST /cash_management/v2/boletos`
 * (produto "Boletos Cobrança - Emissão e Instrução"). O corpo real vem
 * aninhado em `data`; o método do Endpoint desembrulha antes de hidratar.
 *
 * `etapa_processo_boleto` = 'efetivacao' (registro) ou 'validacao' (simulação).
 * A linha digitável e o código de barras saem em
 * `dado_boleto.dados_individuais_boleto[]`.
 */
final class BoletoEmissaoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $etapaProcessoBoleto = null,
        public readonly ?string $codigoCanalOperacao = null,
        public readonly ?string $codigoOperador = null,
        public readonly ?Beneficiario $beneficiario = null,
        public readonly ?DadoBoleto $dadoBoleto = null,
    ) {}
}
