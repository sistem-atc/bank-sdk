<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de transferência consultada (usado na listagem de transferências).
 *
 * Schema `TransferenciaConsultada` da spec "Pix Transferência - Consultar".
 */
final class TransferenciaConsultada implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Dados bancários do pagador. */
        public readonly ?Pagador $pagador = null,
        /** Dados do recebedor. */
        public readonly ?Recebedor $recebedor = null,
        /** Valor da transação. */
        public readonly ?string $valor = null,
        /** EndToEndId da transação (32 posições). */
        public readonly ?string $e2e = null,
        /** ID da transação (TXID). */
        public readonly ?string $idTransacao = null,
        /** Descrição da transação. */
        public readonly ?string $descricao = null,
        /** Data da criação. RFC 3339. */
        public readonly ?string $dataCriacao = null,
        /** CONCLUIDO | EM_PROCESSAMENTO | REJEITADO. */
        public readonly ?string $status = null,
        /** Data da efetivação. Exibida somente quando status = CONCLUIDO. RFC 3339. */
        public readonly ?string $dataEfetivacao = null,
        /** Motivo do cancelamento/rejeição. Exibido somente quando status = REJEITADA. */
        public readonly ?string $motivo = null,
        /** Código do motivo da rejeição. Exibido somente quando status = REJEITADA. */
        public readonly ?string $codigoMotivo = null,
    ) {}
}
