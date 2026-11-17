<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Reports;

use SistemAtc\Banks\Contracts\PagBrasilSftpIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Exceptions\PagBrasilSftpException;
use Throwable;

/**
 * SFTP real (phpseclib 3 ou 4 — o que o host tiver; o namespace mudou de
 * `phpseclib3` pra `phpseclib4`). Conecta na primeira chamada e reaproveita a
 * sessão até disconnect().
 *
 * Chave do servidor: com `getSftpHostFingerprint()` preenchido, a conexão só
 * prossegue se a impressão SHA256 bater — servidor trocado (DNS envenenado,
 * proxy no meio) é recusado ANTES de mandar a senha.
 */
final class PhpseclibSftpTransport implements SftpTransport
{
    public const DEFAULT_HOST = 'sfiles.pagbrasil.com';

    public const DEFAULT_PORT = 22;

    /** @var object|null phpseclib{3,4}\Net\SFTP */
    private ?object $sftp = null;

    private bool $authenticated = false;

    public function __construct(
        private readonly PagBrasilSftpIntegration $integration,
        private readonly int $timeout = 30,
    ) {}

    public function list(string $directory): array
    {
        $sftp = $this->connection();

        try {
            $entries = $sftp->rawlist($directory);
        } catch (Throwable $e) {
            throw new PagBrasilSftpException("PagBrasil SFTP: não listou {$directory}: {$e->getMessage()}", $e);
        }

        if (! is_array($entries)) {
            throw new PagBrasilSftpException("PagBrasil SFTP: não listou {$directory}.");
        }

        $files = [];

        foreach ($entries as $name => $attrs) {
            $name = (string) ($attrs['filename'] ?? $name);

            // SFTP: type 1 = arquivo regular, 2 = diretório.
            if ($name === '.' || $name === '..' || (int) ($attrs['type'] ?? 1) !== 1) {
                continue;
            }

            $files[] = [
                'name' => $name,
                'size' => isset($attrs['size']) ? (int) $attrs['size'] : null,
                'mtime' => isset($attrs['mtime']) ? (int) $attrs['mtime'] : null,
            ];
        }

        return $files;
    }

    public function get(string $path): string
    {
        try {
            $content = $this->connection()->get($path);
        } catch (Throwable $e) {
            throw new PagBrasilSftpException("PagBrasil SFTP: não baixou {$path}: {$e->getMessage()}", $e);
        }

        if (! is_string($content)) {
            throw new PagBrasilSftpException("PagBrasil SFTP: não baixou {$path}.");
        }

        return $content;
    }

    public function disconnect(): void
    {
        if ($this->sftp !== null) {
            try {
                $this->sftp->disconnect();
            } catch (Throwable) {
                // conexão já caída: nada a fazer
            }
            $this->sftp = null;
        }
    }

    /**
     * Impressão SHA256 da chave pública do servidor, no formato do OpenSSH
     * ("SHA256:base64-sem-padding"). Usada pra conferir e pra mostrar ao
     * operador no primeiro acesso.
     */
    public function serverFingerprint(): ?string
    {
        $key = $this->connection(authenticate: false)->getServerPublicHostKey();

        return is_string($key) ? self::fingerprintOf($key) : null;
    }

    /** "ssh-ed25519 AAAA…" → "SHA256:…". */
    public static function fingerprintOf(string $publicKey): ?string
    {
        $parts = explode(' ', trim($publicKey));
        $blob = base64_decode($parts[1] ?? $parts[0], true);

        if ($blob === false || $blob === '') {
            return null;
        }

        return 'SHA256:'.rtrim(base64_encode(hash('sha256', $blob, true)), '=');
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    private function connection(bool $authenticate = true): object
    {
        if ($this->sftp === null) {
            $class = match (true) {
                class_exists('phpseclib4\\Net\\SFTP') => 'phpseclib4\\Net\\SFTP',
                class_exists('phpseclib3\\Net\\SFTP') => 'phpseclib3\\Net\\SFTP',
                default => throw new PagBrasilSftpException('PagBrasil SFTP: instale phpseclib/phpseclib (^3.0 ou ^4.0).'),
            };

            $host = trim($this->integration->getSftpHost()) ?: self::DEFAULT_HOST;
            $port = $this->integration->getSftpPort() ?: self::DEFAULT_PORT;

            try {
                $this->sftp = new $class($host, $port, $this->timeout);
            } catch (Throwable $e) {
                throw new PagBrasilSftpException("PagBrasil SFTP: sem conexão com {$host}:{$port}: {$e->getMessage()}", $e);
            }

            $this->authenticated = false;
        }

        if ($authenticate && ! $this->authenticated) {
            $this->verifyHostKey();
            $this->login();
            $this->authenticated = true;
        }

        return $this->sftp;
    }

    private function verifyHostKey(): void
    {
        $expected = $this->integration->getSftpHostFingerprint();

        if ($expected === null || trim($expected) === '') {
            return;
        }

        $actual = $this->serverFingerprint();

        if ($actual === null || ! hash_equals(self::normalizeFingerprint($expected), self::normalizeFingerprint($actual))) {
            $this->disconnect();

            throw new BankAuthenticationException(
                "PagBrasil SFTP: a chave do servidor ({$actual}) não é a cadastrada — conexão recusada antes de enviar a senha.",
                bank: 'pagbrasil',
            );
        }
    }

    private function login(): void
    {
        $user = trim($this->integration->getSftpUsername());
        $password = $this->integration->getSftpPassword();

        try {
            $ok = $this->sftp->login($user, $password);
        } catch (Throwable $e) {
            throw new BankAuthenticationException("PagBrasil SFTP: falha de autenticação: {$e->getMessage()}", bank: 'pagbrasil');
        }

        if (! $ok) {
            throw new BankAuthenticationException('PagBrasil SFTP: usuário ou senha recusados.', bank: 'pagbrasil');
        }
    }

    private static function normalizeFingerprint(string $value): string
    {
        $value = trim($value);

        return rtrim(str_starts_with($value, 'SHA256:') ? substr($value, 7) : $value, '=');
    }
}
