<?php

declare(strict_types=1);

use SistemAtc\Banks\PagBrasil\Enums\OrderStatus;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\PagBrasil\Reconciliation\SettlementReport;

// Fixture = o CSV de exemplo publicado pela PagBrasil na doc "Reconciliation".
function pbSettlement(): array
{
    return SettlementReport::parse((string) file_get_contents(__DIR__.'/../Fixtures/PagBrasil/settlement-report.csv'));
}

it('lê o relatório de exemplo da PagBrasil por nome de coluna', function () {
    $rows = pbSettlement();
    $first = $rows[0];

    expect($rows)->toHaveCount(63)
        ->and($first->order)->toBe('00000757')
        ->and($first->method())->toBe(PaymentMethod::CreditCard)
        ->and($first->status())->toBe(OrderStatus::Completed)
        ->and($first->amountPaid)->toBe('8885.00')
        ->and($first->processingFee)->toBe('249.27')
        ->and($first->fixedFee)->toBe('0.49')
        ->and($first->variableFee)->toBe('248.78')
        ->and($first->customerTaxid)->toBe('69673251000133')
        ->and($first->recurring)->toBe('Transaction based on initial order')
        ->and($first->date('payment_date')?->format('Y-m-d'))->toBe('2023-02-22');
});

it('detalhe das taxas fecha o total em venda, mas não em estorno/chargeback (tarifa sem coluna)', function () {
    $gaps = [];
    foreach (pbSettlement() as $row) {
        if ($row->fixedFee === null) {
            continue;
        }

        $sum = bcadd(bcadd($row->fixedFee, $row->variableFee ?? '0', 2), bcadd($row->anticipationFee ?? '0', $row->taxes ?? '0', 2), 2);
        $gap = bcsub($row->processingFee ?? '0', $sum, 2);

        if ($gap !== '0.00') {
            $gaps[$row->order] = [$row->orderStatus, $gap];
        }
    }

    expect($gaps)->toBe([
        '00000866' => ['RP', '3.00'],
        '30000866' => ['CB', '25.00'],
        '30000820' => ['CB', '25.00'],
        '30000378' => ['CB', '25.00'],
        'S20230705104704' => ['CD', '0.50'],
    ]);
})->skip(! function_exists('bcadd'), 'bcmath indisponível');

it('normaliza decimal com vírgula e trata o "-" dos lançamentos da PagBrasil', function () {
    $adjustment = collect(pbSettlement())->first(fn ($r) => $r->order === 'S20230705104704');
    $refund = collect(pbSettlement())->first(fn ($r) => $r->amountRefunded === '77.09');

    expect($adjustment->isAdjustment())->toBeTrue()
        ->and($adjustment->customerName)->toBeNull()
        ->and($adjustment->customerTaxid)->toBeNull()
        ->and($adjustment->processingFee)->toBe('0.50')
        ->and($refund)->not->toBeNull()
        ->and($refund->processingFee)->toBe('25.00');
});

it('resolve a data trocada (DD/MM) que aparece no meio do arquivo', function () {
    expect(SettlementReport::date('17/04/2023')?->format('Y-m-d'))->toBe('2023-04-17')
        ->and(SettlementReport::date('02/22/2023')?->format('Y-m-d'))->toBe('2023-02-22')
        ->and(SettlementReport::date('03/07/2023')?->format('Y-m-d'))->toBe('2023-03-07') // ambíguo: vale o padrão MM/DD
        ->and(SettlementReport::date('99/99/2023'))->toBeNull()
        ->and(SettlementReport::date(''))->toBeNull();
});

it('recusa arquivo que não é Settlement Report', function () {
    expect(fn () => SettlementReport::parse("foo;bar\n1;2"))->toThrow(InvalidArgumentException::class)
        ->and(SettlementReport::parse(''))->toBe([]);
});
