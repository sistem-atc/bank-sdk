<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Conciliacao;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Um lançamento Pix conciliado (`GET /conciliacao/v1/lancamentos-pix[/{id}]`).
 *
 * Captura os campos de topo úteis para conciliação (identificação, visão,
 * operação, tipo, canal, txid/e2eid). Os blocos aninhados ricos —
 * `recorrencia` e `detalhe_pagamento` — ficam como array cru, hidratados sob
 * demanda pelo consumidor (o contrato detalhado varia por sub-tipo de Pix).
 */
final class LancamentoPix implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<string, mixed>  $recorrencia
     * @param  array<string, mixed>  $detalhePagamento
     */
    public function __construct(
        public readonly ?string $idLancamento = null,
        public readonly ?string $tipoLancamento = null,
        public readonly ?string $visao = null,
        public readonly ?string $tipoOperacao = null,
        public readonly ?string $tipoPix = null,
        public readonly ?string $subTipoPix = null,
        public readonly ?string $literalLancamento = null,
        public readonly ?string $canalOperacao = null,
        public readonly ?string $txid = null,
        public readonly ?string $e2eid = null,
        public readonly ?bool $devolvido = null,
        public readonly array $recorrencia = [],
        public readonly array $detalhePagamento = [],
    ) {}
}
