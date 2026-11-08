<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Console;

use Illuminate\Console\Command;
use SistemAtc\Banks\Webhooks\Receivers\RequiresClientCertificate;
use SistemAtc\Banks\Webhooks\Receivers\WebhookReceivers;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Imprime o que o servidor web precisa pra receber o webhook de um banco que
 * se autentica por certificado de cliente (hoje, o Inter): o certificado da
 * CA (que vem no pacote), onde instalá-lo e o trecho do nginx.
 *
 * Não escreve nada fora do terminal: o nginx costuma estar em outra máquina
 * (proxy) e exige root. Com `--ca` sai só o PEM, pra encadear:
 *
 *   php artisan banks:webhooks-nginx inter --ca | ssh proxy 'sudo tee /etc/nginx/certs/inter-webhook-ca.crt'
 */
final class WebhooksNginxCommand extends Command
{
    protected $signature = 'banks:webhooks-nginx
        {bank=inter : Banco da rota de webhook}
        {--upstream= : Destino do proxy_pass (o mesmo do location / do site)}
        {--ca-dir=/etc/nginx/certs : Pasta do certificado no servidor do nginx}
        {--ca : Imprime só o certificado da CA (PEM)}';

    protected $description = 'Mostra o certificado da CA e a configuração do nginx para receber webhooks do banco';

    /** Avisa quando algum certificado da CA vence em menos que isto. */
    private const AVISO_VENCIMENTO_DIAS = 90;

    public function handle(): int
    {
        $bank = (string) $this->argument('bank');
        $receiver = WebhookReceivers::for($bank);

        if ($receiver === null) {
            $this->error("Banco sem rota de webhook no SDK: {$bank}.");

            return self::FAILURE;
        }

        if (! $receiver instanceof RequiresClientCertificate) {
            $this->info("O {$bank} não se autentica por certificado: o nginx não precisa de configuração extra.");

            return self::SUCCESS;
        }

        $pem = @file_get_contents($receiver->clientCaPath());

        if ($pem === false || $pem === '') {
            $this->error('Certificado da CA não encontrado no pacote: '.$receiver->clientCaPath());

            return self::FAILURE;
        }

        if ($this->option('ca')) {
            $this->output->write($pem, false, OutputInterface::OUTPUT_RAW);

            return self::SUCCESS;
        }

        $destino = rtrim((string) $this->option('ca-dir'), '/').'/'.$receiver->clientCaFileName();
        $upstream = (string) ($this->option('upstream') ?: 'http://<app>');
        $header = $receiver->clientVerifyHeader();
        $location = '/'.trim((string) config('banks.webhooks.prefix', 'api/webhooks/banks'), '/')."/{$bank}/";

        $this->line("<options=bold>Webhook {$bank}: certificado de cliente conferido pelo nginx</>");
        $this->newLine();
        $this->certificados($pem);

        if ($header === '') {
            $this->warn('banks.webhooks.'.$bank.'.client_verify_header está vazio: o app aceita aviso sem conferir a origem.');
            $this->newLine();
        }

        $this->line('<options=bold>1.</> Instale a CA no servidor que termina o HTTPS:');
        $this->line("   php artisan banks:webhooks-nginx {$bank} --ca | sudo tee {$destino} >/dev/null");
        $this->line("   (nginx em outra máquina: … --ca | ssh <proxy> 'sudo tee {$destino} >/dev/null')");
        $this->newLine();
        $this->line('<options=bold>2.</> No bloco <comment>server { listen 443 ssl; … }</comment> do domínio, antes do <comment>location /</comment>:');
        $this->newLine();
        // Linha a linha: cada linha vira uma escrita própria no terminal.
        foreach (explode("\n", $this->trechoServer($destino, $location, $upstream, $header)) as $linha) {
            $this->line($linha);
        }
        $this->newLine();
        $this->line('<options=bold>3.</> Dentro do <comment>location /</comment> que já existe, pra ninguém forjar o resultado:');
        $this->line("       proxy_set_header {$header} \"\";");
        $this->newLine();
        $this->line('<options=bold>4.</> Aplique: <comment>sudo nginx -t && sudo systemctl reload nginx</comment>');
        $this->newLine();
        $this->line('<options=bold>5.</> Confira: sem certificado do banco a rota tem que dar 403 (o resto do site segue normal):');
        $this->line("   curl -s -o /dev/null -w '%{http_code}\\n' -X POST {$this->baseUrl()}{$location}0/teste -d '[]'");

        if ($upstream === 'http://<app>') {
            $this->newLine();
            $this->warn('Troque http://<app> pelo destino do proxy_pass do site (ou passe --upstream=).');
        }

        return self::SUCCESS;
    }

    private function trechoServer(string $destino, string $location, string $upstream, string $header): string
    {
        return <<<NGINX
            ssl_client_certificate {$destino};
            ssl_verify_client optional;
            ssl_verify_depth 3;

            location {$location} {
                if (\$ssl_client_verify != SUCCESS) {
                    return 403;
                }
                proxy_pass {$upstream};
                proxy_set_header Host \$host;
                proxy_set_header X-Real-IP \$remote_addr;
                proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
                proxy_set_header X-Forwarded-Proto \$scheme;
                proxy_set_header {$header} \$ssl_client_verify;
            }
        NGINX;
    }

    /** Lista cada certificado do bundle com o vencimento. */
    private function certificados(string $pem): void
    {
        preg_match_all('/-----BEGIN CERTIFICATE-----.+?-----END CERTIFICATE-----/s', $pem, $blocos);

        foreach ($blocos[0] as $bloco) {
            $info = openssl_x509_parse($bloco);

            if ($info === false) {
                continue;
            }

            $cn = trim((string) ($info['subject']['CN'] ?? '?'));
            $vence = (int) ($info['validTo_time_t'] ?? 0);
            $dias = (int) floor(($vence - time()) / 86400);
            $quando = date('d/m/Y', $vence);

            match (true) {
                $dias < 0 => $this->error("   {$cn}: VENCIDO em {$quando} — baixe o novo no Internet Banking"),
                $dias < self::AVISO_VENCIMENTO_DIAS => $this->warn("   {$cn}: vence em {$quando} ({$dias} dias)"),
                default => $this->line("   {$cn}: válido até {$quando}"),
            };
        }

        $this->newLine();
    }

    private function baseUrl(): string
    {
        return rtrim((string) (config('banks.webhooks.base_url') ?: config('app.url')), '/');
    }
}
