<?php

declare(strict_types=1);

use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\DailySalesEntry;
use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\RemittanceEntry;
use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\SettlementEntry;
use SistemAtc\Banks\Gateways\PagBrasil\Enums\OrderStatus;
use SistemAtc\Banks\Gateways\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\Gateways\PagBrasil\Enums\ReportType;
use SistemAtc\Banks\Gateways\PagBrasil\PagBrasil;
use SistemAtc\Banks\Gateways\PagBrasil\Reconciliation\DailySalesReport;
use SistemAtc\Banks\Gateways\PagBrasil\Reconciliation\RemittanceReport;
use SistemAtc\Banks\Gateways\PagBrasil\Reports\PhpseclibSftpTransport;
use SistemAtc\Banks\Gateways\PagBrasil\Reports\ReportMethods;
use SistemAtc\Banks\Gateways\PagBrasil\Support\Credentials;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;
use SistemAtc\Banks\Tests\Fakes\FakeSftpTransport;

function pbSftpFixture(string $name): string
{
    return (string) file_get_contents(__DIR__.'/../Fixtures/PagBrasil/sftp/'.$name);
}

function pbSftpTransport(): FakeSftpTransport
{
    return new FakeSftpTransport([
        '/reports/remittancereport_17_11_2026.csv' => pbSftpFixture('remittancereport_17_11_2026.csv'),
        '/reports/dailyreport_17_11_2026.csv' => pbSftpFixture('dailyreport_17_11_2026.csv'),
        '/reports/report_17_11_2026.csv' => pbSftpFixture('report_17_11_2026.csv'),
        '/reports/remittancereport_03_11_2026.csv' => pbSftpFixture('remittancereport_17_11_2026.csv'),
        '/reports/LEIAME.txt' => 'nada',
        '/reports/report_31_02_2026.csv' => 'data inválida',
        '/reports/old/report_01_01_2026.csv' => 'em subpasta',
    ]);
}

it('reconhece tipo e data pelo nome do arquivo', function () {
    [$type, $date] = ReportType::parseFilename('remittancereport_17_11_2026.csv');

    expect($type)->toBe(ReportType::Remittance)
        ->and($date->format('Y-m-d'))->toBe('2026-11-17')
        ->and(ReportType::parseFilename('/reports/dailyreport_01_02_2026.csv')[0])->toBe(ReportType::DailySales)
        ->and(ReportType::parseFilename('report_17_11_2026.csv')[0])->toBe(ReportType::Reconciliation)
        ->and(ReportType::parseFilename('report_31_02_2026.csv'))->toBeNull()
        ->and(ReportType::parseFilename('outro_17_11_2026.csv'))->toBeNull()
        ->and(ReportType::parseFilename('report_17_11_2026.txt'))->toBeNull();
});

it('lê o relatório de Remessa com BOM, aspas e líquido negativo do estorno', function () {
    $rows = RemittanceReport::parse(pbSftpFixture('remittancereport_17_11_2026.csv'));

    expect($rows)->toHaveCount(3)->each->toBeInstanceOf(RemittanceEntry::class);

    [$venda, $pix, $estorno] = $rows;

    expect($venda->order)->toBe('58320')
        ->and($venda->date('remittance_date')?->format('Y-m-d'))->toBe('2026-11-17')
        ->and($venda->date('payment_date')?->format('Y-m-d'))->toBe('2026-11-03')
        ->and($venda->method())->toBe(PaymentMethod::CreditCard)
        ->and($venda->orderStatus())->toBe(OrderStatus::Completed)
        ->and($venda->installmentsCount())->toBe(3)
        ->and($venda->amountPaid)->toBe('249.90')
        ->and($venda->totalFees)->toBe('9.87')
        ->and($venda->netAmount)->toBe('240.03')
        ->and($venda->cardBrand)->toBe('Visa')
        ->and($pix->installmentsCount())->toBeNull()
        ->and($pix->cardBrand)->toBeNull()
        ->and($pix->refundDate)->toBeNull()
        ->and($estorno->orderStatus())->toBe(OrderStatus::RefundProcessed)
        ->and($estorno->netAmount)->toBe('-92.90')
        ->and($estorno->date('refund_date')?->format('Y-m-d'))->toBe('2026-11-12');
});

it('Remessa localiza colunas pelo nome, em qualquer ordem e caixa', function () {
    $csv = "NET AMOUNT;Order Number;Remittance Date\n1.234,56;A1;11/7/2026\n";
    $row = RemittanceReport::parse($csv)[0];

    expect($row->order)->toBe('A1')
        ->and($row->netAmount)->toBe('1234.56')
        ->and($row->date('remittance_date')?->format('Y-m-d'))->toBe('2026-11-07');
});

it('recusa CSV que não é de Remessa', function () {
    RemittanceReport::parse(pbSftpFixture('report_17_11_2026.csv'));
})->throws(InvalidArgumentException::class);

it('lê Vendas diárias com apóstrofo tipográfico no cabeçalho e "-" como vazio', function () {
    $rows = DailySalesReport::parse(pbSftpFixture('dailyreport_17_11_2026.csv'));

    expect($rows)->toHaveCount(2)->each->toBeInstanceOf(DailySalesEntry::class);

    expect($rows[0]->order)->toBe('58320')
        ->and($rows[0]->nsu)->toBe('123456')
        ->and($rows[0]->tid)->toBe('10069930690009F1234A')
        ->and($rows[0]->cardNumber)->toBe('411111******1111')
        ->and($rows[0]->processingFee)->toBe('9.87')
        ->and($rows[0]->customerTaxid)->toBe('12345678909')
        ->and($rows[0]->paramUrl)->toBe('utm=site')
        ->and($rows[1]->status())->toBe(OrderStatus::Failed)
        ->and($rows[1]->customerEmail)->toBeNull()
        ->and($rows[1]->errorCode)->toBe('E01');
});

it('a Conciliação do SFTP é lida pelo mesmo leitor da liquidação', function () {
    $rows = ReportMethods::ler(ReportType::Reconciliation, pbSftpFixture('report_17_11_2026.csv'));

    expect($rows)->toHaveCount(1)
        ->and($rows[0])->toBeInstanceOf(SettlementEntry::class)
        ->and($rows[0]->processingFee)->toBe('9.87')
        ->and($rows[0]->anticipationFee)->toBe('0.75');
});

it('lista só relatórios conhecidos de /reports/, ordenados, com filtro de tipo e data', function () {
    $relatorios = PagBrasil::make()->relatorios(new FakePagBrasilIntegration(), pbSftpTransport());

    $todos = $relatorios->listar();

    expect(array_map(fn ($f) => $f->name, $todos))->toBe([
        'remittancereport_03_11_2026.csv',
        'dailyreport_17_11_2026.csv',
        'remittancereport_17_11_2026.csv',
        'report_17_11_2026.csv',
    ])
        ->and($todos[0]->path)->toBe('/reports/remittancereport_03_11_2026.csv')
        ->and($todos[0]->size)->toBeGreaterThan(0)
        ->and($todos[0]->modifiedAt?->getTimestamp())->toBe(1763380800);

    $remessas = $relatorios->listar(ReportType::Remittance, new DateTimeImmutable('2026-11-10'));

    expect($remessas)->toHaveCount(1)
        ->and($remessas[0]->name)->toBe('remittancereport_17_11_2026.csv');
});

it('baixa e lê em DTOs', function () {
    $transport = pbSftpTransport();
    $relatorios = PagBrasil::make()->relatorios(new FakePagBrasilIntegration(), $transport);
    $arquivo = $relatorios->listar(ReportType::Remittance)[1];

    $linhas = $relatorios->baixarELer($arquivo);
    $cru = $relatorios->baixar('remittancereport_17_11_2026.csv');
    $relatorios->desconectar();

    expect($linhas)->toHaveCount(3)
        ->and($linhas[0])->toBeInstanceOf(RemittanceEntry::class)
        ->and($cru)->toBe(pbSftpFixture('remittancereport_17_11_2026.csv'))
        ->and($transport->downloaded)->toBe(['/reports/remittancereport_17_11_2026.csv', '/reports/remittancereport_17_11_2026.csv'])
        ->and($transport->disconnected)->toBeTrue();
});

it('exige credencial de SFTP', function () {
    expect(fn () => Credentials::sftp(new FakeBankIntegration()))
        ->toThrow(BankAuthenticationException::class, 'PagBrasilSftpIntegration')
        ->and(fn () => Credentials::sftp(new FakePagBrasilIntegration(sftpPassword: '')))
        ->toThrow(BankAuthenticationException::class, 'usuário e senha do SFTP')
        ->and(fn () => Credentials::sftp(new FakePagBrasilIntegration(active: false)))
        ->toThrow(BankAuthenticationException::class, 'inativa')
        ->and(fn () => PagBrasil::make()->relatorios(new FakePagBrasilIntegration(sftpUsername: ' ')))
        ->toThrow(BankAuthenticationException::class);
});

it('calcula a impressão SHA256 da chave do servidor no formato do OpenSSH', function () {
    // Chave ed25519 de teste: blob = "\0\0\0\x0bssh-ed25519\0\0\0 " + 32 bytes.
    $blob = "\0\0\0\x0bssh-ed25519\0\0\0\x20".str_repeat("\x01", 32);
    $key = 'ssh-ed25519 '.base64_encode($blob);

    expect(PhpseclibSftpTransport::fingerprintOf($key))
        ->toBe('SHA256:'.rtrim(base64_encode(hash('sha256', $blob, true)), '='))
        ->and(PhpseclibSftpTransport::fingerprintOf('lixo!!'))->toBeNull();
});

it('aceita o nome REAL do servidor: conta colada ao tipo e data com hífen', function () {
    [$type, $date, $conta] = ReportType::parseFilename('remittancereport1898_28-10-2026.csv');

    expect($type)->toBe(ReportType::Remittance)
        ->and($date->format('Y-m-d'))->toBe('2026-10-28')
        ->and($conta)->toBe('1898')
        ->and(ReportType::parseFilename('report1898_08-04-2026.csv')[0])->toBe(ReportType::Reconciliation)
        ->and(ReportType::parseFilename('dailyreport1898_16-11-2026.csv')[0])->toBe(ReportType::DailySales)
        ->and(ReportType::parseFilename('report_17_11_2026.csv')[2])->toBeNull()
        ->and(ReportType::parseFilename('report1898_31-02-2026.csv'))->toBeNull();

    $relatorios = PagBrasil::make()->relatorios(new FakePagBrasilIntegration(), new FakeSftpTransport([
        '/reports/report1898_08-04-2026.csv' => 'x',
        '/reports/remittancereport1898_28-10-2026.csv' => 'y',
    ]));

    expect(array_map(fn ($f) => [$f->name, $f->accountCode], $relatorios->listar()))->toBe([
        ['report1898_08-04-2026.csv', '1898'],
        ['remittancereport1898_28-10-2026.csv', '1898'],
    ]);
});

it('lê a Remessa REAL: data ISO, decimal com vírgula e BOM', function () {
    $rows = RemittanceReport::parse(pbSftpFixture('remittancereport1898_28-10-2026.csv'));

    expect($rows)->toHaveCount(2)
        ->and($rows[0]->order)->toBe('ac4a86e9e7d07fad')
        ->and($rows[0]->date('remittance_date')?->format('Y-m-d'))->toBe('2026-10-28')
        ->and($rows[0]->date('report_date')?->format('Y-m-d'))->toBe('2026-10-23')
        ->and($rows[0]->date('payment_date')?->format('Y-m-d'))->toBe('2026-08-23')
        ->and($rows[0]->amountPaid)->toBe('410.90')
        ->and($rows[0]->totalFees)->toBe('13.23')
        ->and($rows[0]->netAmount)->toBe('397.67');
});
