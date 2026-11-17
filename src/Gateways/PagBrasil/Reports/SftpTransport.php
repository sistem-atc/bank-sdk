<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Reports;

/**
 * O mínimo de SFTP que os relatórios precisam: listar uma pasta e baixar um
 * arquivo. Existe pra que o host (e os testes) troquem o transporte real
 * (PhpseclibSftpTransport) por um em memória.
 */
interface SftpTransport
{
    /**
     * Arquivos REGULARES da pasta (subpastas e "."/".." ficam de fora).
     *
     * @return list<array{name: string, size: int|null, mtime: int|null}>
     */
    public function list(string $directory): array;

    /** Conteúdo do arquivo. Lança exceção se não conseguir baixar. */
    public function get(string $path): string;

    public function disconnect(): void;
}
