<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resultado da solicitação de transferência Pix (SPI).
 *
 * Schema `SolicitarTransferenciaResponse` da spec "Pix Transferência -
 * Transferir" — POST /v1/spi/solicitar-transferencia (HTTP 200 = transação
 * realizada; HTTP 202 = transação rejeitada, com `codigoMotivo` preenchido).
 *
 * ⚠️ MOVIMENTA DINHEIRO. O identificador de idempotência é o `idTransacao`
 * (TXID), atribuído pela empresa pagadora: segundo a spec, "não poderá ser
 * reutilizado em outra transação". Status EM_PROCESSAMENTO deve ser
 * reconsultado (recomendação da spec: em até 1 minuto) — NUNCA reenviar a
 * solicitação com outro idTransacao para o mesmo pagamento.
 */
final class SolicitarTransferenciaResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Dados bancários do pagador. */
        public readonly ?PagadorResponse $pagador = null,
        /** Dados do recebedor. */
        public readonly ?RecebedorResponse $recebedor = null,
        /** Valor da transação (string, separador decimal ponto). */
        public readonly ?string $valor = null,
        /** EndToEndId da transação (32 posições). */
        public readonly ?string $e2e = null,
        /** ID da transação (TXID) — identificador de idempotência do pagador. */
        public readonly ?string $idTransacao = null,
        /** Descrição da transação (até 30 posições). */
        public readonly ?string $descricao = null,
        /** Data da criação. RFC 3339. */
        public readonly ?string $dataCriacao = null,
        /** CONCLUIDO | EM_PROCESSAMENTO | REJEITADO. */
        public readonly ?string $status = null,
        /** Mensagem descritiva do resultado da solicitação. */
        public readonly ?string $motivo = null,
        /** Código do motivo da rejeição. Exibido somente quando status = REJEITADO. */
        public readonly ?string $codigoMotivo = null,
    ) {}
}
