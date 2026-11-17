<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Tests\Fakes;

use RuntimeException;
use SistemAtc\Banks\Gateways\PagBrasil\Reports\SftpTransport;

/** SFTP em memória: path => conteúdo. Diretórios declarados à parte. */
final class FakeSftpTransport implements SftpTransport
{
    /** @var list<string> */
    public array $downloaded = [];

    public bool $disconnected = false;

    /**
     * @param  array<string, string>  $files  path completo => conteúdo
     * @param  list<string>  $directories  subpastas (devem ser ignoradas)
     */
    public function __construct(
        private array $files = [],
        private readonly array $directories = [],
        private readonly int $mtime = 1763380800,
    ) {}

    public function list(string $directory): array
    {
        $prefix = rtrim($directory, '/').'/';
        $list = [];

        foreach ($this->files as $path => $content) {
            if (str_starts_with($path, $prefix) && ! str_contains(substr($path, strlen($prefix)), '/')) {
                $list[] = ['name' => substr($path, strlen($prefix)), 'size' => strlen($content), 'mtime' => $this->mtime];
            }
        }

        return $list;
    }

    public function get(string $path): string
    {
        $this->downloaded[] = $path;

        return $this->files[$path] ?? throw new RuntimeException("not found: {$path}");
    }

    public function disconnect(): void
    {
        $this->disconnected = true;
    }
}
