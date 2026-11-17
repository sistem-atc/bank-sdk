<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Reports;

use DateTimeImmutable;
use SistemAtc\Banks\Gateways\PagBrasil\Enums\ReportType;

/**
 * Um relatório disponível no SFTP da PagBrasil. `date` é a data do NOME do
 * arquivo (DD_MM_YYYY); `modifiedAt` e `size` vêm do servidor e mudam quando a
 * PagBrasil regenera o arquivo numa janela seguinte — o host usa os dois pra
 * saber se precisa baixar de novo.
 */
final class ReportFile
{
    public function __construct(
        public readonly string $name,
        public readonly string $path,
        public readonly ReportType $type,
        public readonly DateTimeImmutable $date,
        public readonly ?int $size = null,
        public readonly ?DateTimeImmutable $modifiedAt = null,
    ) {}

    /** @param array{name: string, size: int|null, mtime: int|null} $remote */
    public static function fromRemote(string $directory, array $remote): ?self
    {
        $parsed = ReportType::parseFilename($remote['name']);

        if ($parsed === null) {
            return null;
        }

        return new self(
            name: $remote['name'],
            path: rtrim($directory, '/').'/'.$remote['name'],
            type: $parsed[0],
            date: $parsed[1],
            size: $remote['size'],
            modifiedAt: $remote['mtime'] !== null ? (new DateTimeImmutable('@'.$remote['mtime'])) : null,
        );
    }

    /** @return array{name: string, path: string, type: string, date: string, size: int|null, modified_at: string|null} */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'type' => $this->type->value,
            'date' => $this->date->format('Y-m-d'),
            'size' => $this->size,
            'modified_at' => $this->modifiedAt?->format(DATE_ATOM),
        ];
    }
}
