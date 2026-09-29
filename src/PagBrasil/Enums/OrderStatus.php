<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** `order_status` de /api/order/get (e do relatório de liquidação). */
enum OrderStatus: string
{
    case WaitingPayment = 'WP';
    case PreAuthorized = 'PA';
    case Completed = 'PC';
    case Failed = 'PF';
    /** Recusado pela antifraude ou, em Pix/boleto, CANCELADO via /api/order/cancel. */
    case Rejected = 'PR';
    case RefundRequested = 'RR';
    case RefundProcessed = 'RP';
    case Chargeback = 'CB';
    /** Débito/crédito lançado pela PagBrasil (só no relatório de liquidação). */
    case PagBrasilAdjustment = 'CD';

    public function label(): string
    {
        return match ($this) {
            self::WaitingPayment => 'Aguardando pagamento',
            self::PreAuthorized => 'Pré-autorizado (não capturado)',
            self::Completed => 'Pago',
            self::Failed => 'Falhou',
            self::Rejected => 'Rejeitado',
            self::RefundRequested => 'Estorno solicitado',
            self::RefundProcessed => 'Estorno processado',
            self::Chargeback => 'Chargeback',
            self::PagBrasilAdjustment => 'Débito/crédito PagBrasil',
        };
    }

    /** O dinheiro entrou (e ainda não saiu por estorno/chargeback)? */
    public function isPaid(): bool
    {
        return $this === self::Completed;
    }
}
