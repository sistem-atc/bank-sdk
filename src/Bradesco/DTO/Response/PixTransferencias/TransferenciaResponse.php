<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Transferência Pix retornada pelas consultas.
 *
 * Schema `TransferenciaResponse` da spec "Pix Transferência - Consultar" —
 * GET /v1/spi/consulta/transferencia/{id-transacao} e
 * GET /v1/spi/consulta/transferencia/{id-transacao}/{e2e}.
 *
 * Transações com status EM_PROCESSAMENTO devem ser reconsultadas por este
 * serviço (recomendação da spec: em até 1 minuto) para obter o status final.
 */
final class TransferenciaResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Dados bancários do pagador. */
        public readonly ?Pagador $pagador = null,
        /** Dados do recebedor. */
        public readonly ?RecebedorResponse $recebedor = null,
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
        /** Data da efetivação. RFC 3339. */
        public readonly ?string $dataEfetivacao = null,
        /** Mensagem descritiva do resultado da solicitação. */
        public readonly ?string $motivo = null,
    ) {}
}
