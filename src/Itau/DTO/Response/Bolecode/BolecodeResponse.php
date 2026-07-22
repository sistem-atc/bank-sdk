<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta da emissão de Bolecode Pix — `POST /recebimentos-pix/v1/boletos_pix`.
 *
 * O corpo de sucesso (200) espelha o body de entrada e acrescenta os dados
 * gerados (código de barras / linha digitável em `dado_boleto >
 * dados_individuais_boleto`, `nome_cobranca` do beneficiário etc.). A API pode
 * também responder 202 (processamento assíncrono) com apenas `{codigo, mensagem}`
 * — "Operação em andamento, consulte seu bolecode em instantes" — daí os campos
 * `codigo`/`mensagem` opcionais. O payload de sucesso vem embrulhado em `data`;
 * o método do Endpoint desembrulha antes de hidratar.
 */
final class BolecodeResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigo = null,
        public readonly ?string $mensagem = null,
        public readonly ?string $etapaProcessoBoleto = null,
        public readonly ?string $codigoCanalOperacao = null,
        public readonly ?string $codigoOperador = null,
        public readonly ?Beneficiario $beneficiario = null,
        public readonly ?DadoBoleto $dadoBoleto = null,
    ) {}
}
