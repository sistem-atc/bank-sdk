<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Sispag;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta da inclusão de Pix no SISPAG — `POST /sispag/v1/transferencias`
 * (Pix por dados, por chave ou QR Code).
 *
 * Campos conforme a doc da API Cash Management. `status_pagamento` ∈
 * {Sucesso, Sucesso (pre-autorizado), Rejeitado, Nao incluido}. Valores
 * monetários vêm como string decimal ("1260.00"); `data_pagamento` na resposta
 * é ISO com hora ("2016-10-11T12:00:00").
 */
final class TransferenciaResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $statusPagamento = null,
        public readonly ?string $codPagamento = null,
        public readonly ?string $numeroLote = null,
        public readonly ?string $numeroLancamento = null,
        public readonly ?string $tipoPagamento = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $valorPagamento = null,
        public readonly ?string $identificacaoComprovante = null,
        public readonly ?string $informacoesEntreUsuarios = null,
        public readonly ?Recebedor $recebedor = null,
    ) {}
}
