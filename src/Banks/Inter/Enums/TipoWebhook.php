<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Enums;

/**
 * Todos os avisos (callbacks) que o Inter envia, de qualquer API. O valor é o
 * último segmento da URL que o SDK registra (`.../inter/{integração}/{tipo}`),
 * e é por ele que o recebimento sabe o que chegou.
 */
enum TipoWebhook: string
{
    /** API Pix: Pix recebido (cob/cobv pagos e Pix na chave). Um webhook por chave Pix. */
    case PixRecebido = 'pix';

    /** API Cobrança: boleto com Pix pago, cancelado ou expirado. */
    case Cobranca = 'cobranca';

    /** API Banking: Pix que a empresa enviou (aprovado, falhou, devolvido). */
    case PixPagamento = 'pix-pagamento';

    /** API Banking: boleto que a empresa pagou. */
    case BoletoPagamento = 'boleto-pagamento';

    public function label(): string
    {
        return match ($this) {
            self::PixRecebido => 'Pix recebido',
            self::Cobranca => 'Cobrança paga, cancelada ou expirada (boleto com Pix)',
            self::PixPagamento => 'Pix enviado (confirmação do pagamento)',
            self::BoletoPagamento => 'Boleto pago (confirmação do pagamento)',
        };
    }

    /** Escopo de ESCRITA que o app precisa ter pra cadastrar este webhook. */
    public function escopo(): Escopo
    {
        return match ($this) {
            self::PixRecebido => Escopo::WebhookPixEscrita,
            self::Cobranca => Escopo::BoletoCobrancaEscrita,
            self::PixPagamento, self::BoletoPagamento => Escopo::WebhookBankingEscrita,
        };
    }

    /** O webhook de Pix recebido é cadastrado por chave Pix. */
    public function exigeChavePix(): bool
    {
        return $this === self::PixRecebido;
    }

    /** Equivalente na API Banking, quando é um webhook de lá. */
    public function banking(): ?TipoWebhookBanking
    {
        return match ($this) {
            self::PixPagamento => TipoWebhookBanking::PixPagamento,
            self::BoletoPagamento => TipoWebhookBanking::BoletoPagamento,
            default => null,
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $tipo) {
            $options[$tipo->value] = $tipo->label();
        }

        return $options;
    }
}
