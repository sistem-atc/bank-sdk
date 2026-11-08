<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Enums;

/**
 * Escopos OAuth das APIs Inter Empresas — um por permissão marcada na tela da
 * aplicação (Internet Banking PJ → Soluções para sua empresa → Minhas
 * integrações). O Inter recusa o token se pedir escopo não marcado no app, por
 * isso o host guarda a lista do app (settings['scopes']) e o SDK pede só ela.
 */
enum Escopo: string
{
    // API Banking
    case ExtratoLeitura = 'extrato.read';
    case PagamentoBoletoLeitura = 'pagamento-boleto.read';
    case PagamentoBoletoEscrita = 'pagamento-boleto.write';
    case PagamentoDarfEscrita = 'pagamento-darf.write';
    case PagamentoLoteLeitura = 'pagamento-lote.read';
    case PagamentoLoteEscrita = 'pagamento-lote.write';
    case PagamentoPixLeitura = 'pagamento-pix.read';
    case PagamentoPixEscrita = 'pagamento-pix.write';
    case WebhookBankingLeitura = 'webhook-banking.read';
    case WebhookBankingEscrita = 'webhook-banking.write';

    // API Cobrança (boleto com Pix)
    case BoletoCobrancaLeitura = 'boleto-cobranca.read';
    case BoletoCobrancaEscrita = 'boleto-cobranca.write';

    // API Pix (recebimentos)
    case CobLeitura = 'cob.read';
    case CobEscrita = 'cob.write';
    case CobVLeitura = 'cobv.read';
    case CobVEscrita = 'cobv.write';
    case LoteCobVLeitura = 'lotecobv.read';
    case LoteCobVEscrita = 'lotecobv.write';
    case PayloadLocationLeitura = 'payloadlocation.read';
    case PayloadLocationEscrita = 'payloadlocation.write';
    case PixLeitura = 'pix.read';
    case PixEscrita = 'pix.write';
    case WebhookPixLeitura = 'webhook.read';
    case WebhookPixEscrita = 'webhook.write';

    /** Como a permissão aparece pra quem cadastra (sem o código técnico). */
    public function label(): string
    {
        return match ($this) {
            self::ExtratoLeitura => 'Consultar extrato e saldo',
            self::PagamentoBoletoLeitura => 'Consultar pagamentos de boleto',
            self::PagamentoBoletoEscrita => 'Pagar boleto',
            self::PagamentoDarfEscrita => 'Pagar DARF',
            self::PagamentoLoteLeitura => 'Consultar pagamentos em lote',
            self::PagamentoLoteEscrita => 'Pagar em lote',
            self::PagamentoPixLeitura => 'Consultar Pix enviado',
            self::PagamentoPixEscrita => 'Enviar Pix',
            self::WebhookBankingLeitura => 'Consultar webhooks de pagamento',
            self::WebhookBankingEscrita => 'Cadastrar webhooks de pagamento',
            self::BoletoCobrancaLeitura => 'Consultar cobranças (boleto com Pix)',
            self::BoletoCobrancaEscrita => 'Emitir e alterar cobranças (boleto com Pix)',
            self::CobLeitura => 'Consultar cobrança Pix imediata',
            self::CobEscrita => 'Criar cobrança Pix imediata',
            self::CobVLeitura => 'Consultar cobrança Pix com vencimento',
            self::CobVEscrita => 'Criar cobrança Pix com vencimento',
            self::LoteCobVLeitura => 'Consultar lote de cobranças com vencimento',
            self::LoteCobVEscrita => 'Criar lote de cobranças com vencimento',
            self::PayloadLocationLeitura => 'Consultar locations de QR Code',
            self::PayloadLocationEscrita => 'Criar locations de QR Code',
            self::PixLeitura => 'Consultar Pix recebido',
            self::PixEscrita => 'Devolver Pix recebido',
            self::WebhookPixLeitura => 'Consultar webhook de Pix recebido',
            self::WebhookPixEscrita => 'Cadastrar webhook de Pix recebido',
        };
    }

    /** API do Inter a que o escopo pertence — pra agrupar na tela. */
    public function api(): string
    {
        return match (true) {
            str_starts_with($this->value, 'boleto-cobranca') => 'Cobrança',
            in_array($this, [
                self::ExtratoLeitura, self::PagamentoBoletoLeitura, self::PagamentoBoletoEscrita,
                self::PagamentoDarfEscrita, self::PagamentoLoteLeitura, self::PagamentoLoteEscrita,
                self::PagamentoPixLeitura, self::PagamentoPixEscrita,
                self::WebhookBankingLeitura, self::WebhookBankingEscrita,
            ], true) => 'Banking',
            default => 'Pix',
        };
    }

    /**
     * Opções pra um select: `['extrato.read' => 'Consultar extrato e saldo (extrato.read)', ...]`.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $escopo) {
            $options[$escopo->value] = $escopo->label().' ('.$escopo->value.')';
        }

        return $options;
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_map(fn (self $e) => $e->value, self::cases());
    }
}
