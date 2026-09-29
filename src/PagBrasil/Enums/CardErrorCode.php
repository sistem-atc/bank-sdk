<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/** `error_code` de pedido de cartão (crédito/débito) com status PF/PR. */
enum CardErrorCode: string
{
    case DeclinedByIssuer = '01';
    case ContactIssuer = '02';
    case CardRestricted = '03';
    case TryAgain = '04';
    case Declined = '05';
    case InvalidAmount = '06';
    case InvalidCardNumber = '07';
    case InvalidIssuer = '08';
    case LimitExceeded = '09';
    case ExpiredCard = '10';
    case NotAllowedByBank = '11';
    case CardNotActivated = '12';
    case BankUnavailable = '13';
    case CardNotAllowedForTransaction = '14';
    case InvalidCvv = '15';
    case NotAllowedOnline = '16';
    case InvalidInstallmentPlan = '17';
    case PaypalAgreementCancelled = '18';
    case InvalidExpirationDate = '19';

    public function description(): string
    {
        return match ($this) {
            self::DeclinedByIssuer, self::Declined => 'Recusado pelo banco emissor.',
            self::ContactIssuer => 'Cliente deve contatar o banco emissor.',
            self::CardRestricted => 'Cartão com restrição; cliente deve contatar o banco emissor.',
            self::TryAgain => 'Tente novamente.',
            self::InvalidAmount => 'Valor inválido.',
            self::InvalidCardNumber => 'Número do cartão inválido.',
            self::InvalidIssuer => 'Banco emissor inválido.',
            self::LimitExceeded => 'Limite do cartão excedido.',
            self::ExpiredCard => 'Cartão vencido.',
            self::NotAllowedByBank => 'Transação não permitida pelo banco.',
            self::CardNotActivated => 'Cartão não ativado; cliente deve contatar o banco emissor.',
            self::BankUnavailable => 'Banco indisponível no momento.',
            self::CardNotAllowedForTransaction => 'Cartão não permitido para este tipo de transação.',
            self::InvalidCvv => 'CVV inválido.',
            self::NotAllowedOnline => 'Cartão não permitido para compras online.',
            self::InvalidInstallmentPlan => 'Parcelamento inválido.',
            self::PaypalAgreementCancelled => 'Acordo PayPal já cancelado.',
            self::InvalidExpirationDate => 'Validade do cartão inválida.',
        };
    }
}
