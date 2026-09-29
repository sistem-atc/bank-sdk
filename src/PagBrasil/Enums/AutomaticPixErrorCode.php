<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** Códigos de erro do Pix Automático e a ação que cada um admite. */
enum AutomaticPixErrorCode: string
{
    case ProcessingError = '001';
    case NotAllowedByBank = '002';
    case InvalidAmount = '003';
    case InvalidTaxId = '004';
    case InvalidChargeDate = '005';
    case InvalidRetrySchedule = '006';
    case InvalidRecurrenceId = '007';
    case InvalidRecurrenceStatus = '008';
    case InvalidPaymentInstruction = '009';
    case CancelledByCustomer = '010';
    case TransactionFailed = '011';
    case CancelledByMerchant = '012';

    public function description(): string
    {
        return match ($this) {
            self::ProcessingError => 'Erro ao processar — crie uma nova cobrança (não dá para reenviar a mesma).',
            self::NotAllowedByBank => 'Transação não permitida pelo banco — não é possível cobrar.',
            self::InvalidAmount => 'Valor inválido — pode reenviar com o valor corrigido.',
            self::InvalidTaxId => 'CPF/CNPJ inválido — pode reenviar com o documento corrigido.',
            self::InvalidChargeDate => 'Data de cobrança inválida — não é possível cobrar.',
            self::InvalidRetrySchedule => 'Agendamento de retentativa inválido — só no próximo ciclo.',
            self::InvalidRecurrenceId => 'Identificador de recorrência inválido — não é possível cobrar.',
            self::InvalidRecurrenceStatus => 'Recorrência em status inválido — colete novo consentimento.',
            self::InvalidPaymentInstruction => 'Instrução de pagamento inválida — não é possível cobrar.',
            self::CancelledByCustomer => 'Cancelado pelo cliente — colete novo consentimento.',
            self::TransactionFailed => 'Transação falhou — não é possível cobrar.',
            self::CancelledByMerchant => 'Cancelado pelo lojista — colete novo consentimento.',
        };
    }

    /** O consentimento morreu e é preciso pedir outro ao pagador? */
    public function requiresNewConsent(): bool
    {
        return in_array($this, [self::InvalidRecurrenceStatus, self::CancelledByCustomer, self::CancelledByMerchant], true);
    }
}
