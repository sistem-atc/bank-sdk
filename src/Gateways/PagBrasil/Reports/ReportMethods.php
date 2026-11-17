<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Reports;

use DateTimeInterface;
use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\DailySalesEntry;
use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\RemittanceEntry;
use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\SettlementEntry;
use SistemAtc\Banks\Gateways\PagBrasil\Enums\ReportType;
use SistemAtc\Banks\Gateways\PagBrasil\Reconciliation\DailySalesReport;
use SistemAtc\Banks\Gateways\PagBrasil\Reconciliation\RemittanceReport;
use SistemAtc\Banks\Gateways\PagBrasil\Reconciliation\SettlementReport;

/**
 * Relatórios da PagBrasil via SFTP (sfiles.pagbrasil.com:22, pasta /reports/).
 *
 *   $relatorios = PagBrasil::make()->relatorios($integracao);
 *   foreach ($relatorios->listar(ReportType::Remittance, desde: new DateTimeImmutable('-7 days')) as $arquivo) {
 *       $csv = $relatorios->baixar($arquivo);        // conteúdo cru (guarde-o)
 *       $linhas = ReportMethods::ler($arquivo->type, $csv); // DTOs tipados
 *   }
 *
 * A PagBrasil gera os arquivos em três janelas por dia (10–12h, 14–16h,
 * 18–20h, horário de Brasília), redundantes: um arquivo pode ser REGERADO
 * com o mesmo nome numa janela seguinte. Os arquivos não expiram no servidor.
 * Sem transação no período, não há arquivo.
 */
final class ReportMethods
{
    public const DIRECTORY = '/reports/';

    public function __construct(
        private readonly SftpTransport $transport,
        private readonly string $directory = self::DIRECTORY,
    ) {}

    /**
     * Relatórios disponíveis, do mais antigo pro mais novo. Arquivo com nome
     * fora do padrão é ignorado.
     *
     * @param  ReportType|null  $type  só um tipo (null = todos)
     * @param  DateTimeInterface|null  $desde  só arquivos com data (do nome) ≥ esta
     * @return list<ReportFile>
     */
    public function listar(?ReportType $type = null, ?DateTimeInterface $desde = null): array
    {
        $files = [];

        foreach ($this->transport->list($this->directory) as $remote) {
            $file = ReportFile::fromRemote($this->directory, $remote);

            if ($file === null || ($type !== null && $file->type !== $type)) {
                continue;
            }

            if ($desde !== null && $file->date->format('Y-m-d') < $desde->format('Y-m-d')) {
                continue;
            }

            $files[] = $file;
        }

        usort($files, fn (ReportFile $a, ReportFile $b) => [$a->date, $a->type->value, $a->name] <=> [$b->date, $b->type->value, $b->name]);

        return $files;
    }

    /** Conteúdo cru do arquivo (CSV). */
    public function baixar(ReportFile|string $file): string
    {
        $path = $file instanceof ReportFile
            ? $file->path
            : (str_starts_with($file, '/') ? $file : rtrim($this->directory, '/').'/'.$file);

        return $this->transport->get($path);
    }

    /**
     * Baixa e lê: DTO por linha conforme o tipo.
     *
     * @return list<SettlementEntry>|list<DailySalesEntry>|list<RemittanceEntry>
     */
    public function baixarELer(ReportFile $file): array
    {
        return self::ler($file->type, $this->baixar($file));
    }

    /**
     * Lê um CSV já baixado (o host guarda o cru e pode reprocessar sem SFTP).
     *
     * @return list<SettlementEntry>|list<DailySalesEntry>|list<RemittanceEntry>
     */
    public static function ler(ReportType $type, string $csv): array
    {
        return match ($type) {
            ReportType::Reconciliation => SettlementReport::parse($csv),
            ReportType::DailySales => DailySalesReport::parse($csv),
            ReportType::Remittance => RemittanceReport::parse($csv),
        };
    }

    public function desconectar(): void
    {
        $this->transport->disconnect();
    }
}
