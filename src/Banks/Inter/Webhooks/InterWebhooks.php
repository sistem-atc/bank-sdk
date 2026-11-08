<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Webhooks;

use InvalidArgumentException;
use SistemAtc\Banks\Banks\Inter\Endpoints\Banking\Banking;
use SistemAtc\Banks\Banks\Inter\Endpoints\Cobranca\Cobranca;
use SistemAtc\Banks\Banks\Inter\Endpoints\Pix\RecebimentosPix;
use SistemAtc\Banks\Banks\Inter\Enums\TipoWebhook;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Exceptions\BankRequestException;
use SistemAtc\Banks\Webhooks\WebhookUrl;

/**
 * Webhooks do Inter num lugar só, com a URL que o próprio SDK recebe:
 *
 *   $webhooks = Inter::make()->webhooks($integration);
 *   $webhooks->cadastrar(TipoWebhook::Cobranca);
 *   $webhooks->cadastrar(TipoWebhook::PixRecebido, chavePix: 'financeiro@empresa.com.br');
 *   $webhooks->sincronizar([TipoWebhook::Cobranca], anteriores: [TipoWebhook::PixPagamento]);
 *
 * Cada tipo vive numa API diferente do Inter (Pix, Cobrança, Banking); esta
 * classe só escolhe a certa. Os métodos por API continuam disponíveis em
 * banking()->webhooks(), cobranca()->webhook() e recebimentosPix()->webhook().
 */
final class InterWebhooks
{
    public function __construct(private readonly BankIntegration $integration) {}

    /** URL registrada no Inter pra este tipo (a rota que o SDK publica). */
    public function url(TipoWebhook $tipo): string
    {
        return WebhookUrl::for('inter', $this->integration, $tipo->value);
    }

    /** Cadastra (ou troca) o webhook apontando pro SDK. Devolve a URL registrada. */
    public function cadastrar(TipoWebhook $tipo, ?string $chavePix = null): string
    {
        $url = $this->url($tipo);

        match ($tipo) {
            TipoWebhook::PixRecebido => $this->pix()->webhook()->cadastrar(self::chave($tipo, $chavePix), $url),
            TipoWebhook::Cobranca => $this->cobranca()->webhook()->cadastrar($url),
            default => $this->banking()->webhooks()->cadastrar($tipo->banking(), $url),
        };

        return $url;
    }

    /** URL cadastrada hoje no Inter, ou null quando não há webhook. */
    public function consultar(TipoWebhook $tipo, ?string $chavePix = null): ?string
    {
        try {
            return match ($tipo) {
                TipoWebhook::PixRecebido => $this->pix()->webhook()->consultar(self::chave($tipo, $chavePix))->webhookUrl,
                TipoWebhook::Cobranca => $this->cobranca()->webhook()->consultar()->webhookUrl,
                default => $this->banking()->webhooks()->consultar($tipo->banking())->webhookUrl,
            };
        } catch (BankRequestException $e) {
            if ($e->status() === 404) {
                return null;
            }

            throw $e;
        }
    }

    /** Remove o webhook. Não existir já é o resultado pedido. */
    public function excluir(TipoWebhook $tipo, ?string $chavePix = null): void
    {
        try {
            match ($tipo) {
                TipoWebhook::PixRecebido => $this->pix()->webhook()->excluir(self::chave($tipo, $chavePix)),
                TipoWebhook::Cobranca => $this->cobranca()->webhook()->excluir(),
                default => $this->banking()->webhooks()->excluir($tipo->banking()),
            };
        } catch (BankRequestException $e) {
            if ($e->status() !== 404) {
                throw $e;
            }
        }
    }

    /**
     * Deixa no Inter exatamente os tipos desejados: cadastra cada desejado e
     * remove os que saíram da lista (`anteriores` que não estão em `desejados`).
     * Um tipo que falha não impede os outros.
     *
     * @param  list<TipoWebhook|string>  $desejados
     * @param  list<TipoWebhook|string>  $anteriores
     * @return array<string, array{acao: string, ok: bool, detalhe: string}>
     */
    public function sincronizar(array $desejados, array $anteriores = [], ?string $chavePix = null): array
    {
        $desejados = self::tipos($desejados);
        $remover = array_diff_key(self::tipos($anteriores), $desejados);
        $resultado = [];

        foreach ($desejados as $valor => $tipo) {
            $resultado[$valor] = $this->tentar('cadastrar', fn () => $this->cadastrar($tipo, $chavePix));
        }

        foreach ($remover as $valor => $tipo) {
            $resultado[$valor] = $this->tentar('excluir', function () use ($tipo, $chavePix) {
                $this->excluir($tipo, $chavePix);

                return 'removido';
            });
        }

        return $resultado;
    }

    /** @return array{acao: string, ok: bool, detalhe: string} */
    private function tentar(string $acao, callable $fn): array
    {
        try {
            return ['acao' => $acao, 'ok' => true, 'detalhe' => (string) $fn()];
        } catch (BankRequestException|BankAuthenticationException|InvalidArgumentException $e) {
            return ['acao' => $acao, 'ok' => false, 'detalhe' => $e->getMessage()];
        }
    }

    /**
     * @param  list<TipoWebhook|string>  $tipos
     * @return array<string, TipoWebhook>
     */
    private static function tipos(array $tipos): array
    {
        $out = [];

        foreach ($tipos as $tipo) {
            $tipo = $tipo instanceof TipoWebhook ? $tipo : TipoWebhook::tryFrom((string) $tipo);

            if ($tipo !== null) {
                $out[$tipo->value] = $tipo;
            }
        }

        return $out;
    }

    private static function chave(TipoWebhook $tipo, ?string $chavePix): string
    {
        if ($chavePix === null || trim($chavePix) === '') {
            throw new InvalidArgumentException(
                "Inter: o webhook \"{$tipo->label()}\" é cadastrado por chave Pix — informe a chave."
            );
        }

        return trim($chavePix);
    }

    private function banking(): Banking
    {
        return new Banking($this->integration);
    }

    private function cobranca(): Cobranca
    {
        return new Cobranca($this->integration);
    }

    private function pix(): RecebimentosPix
    {
        return new RecebimentosPix($this->integration);
    }
}
