<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Exceptions\PagBrasilRequestException;
use SistemAtc\Banks\PagBrasil\DTO\Response\ApiResult;
use SistemAtc\Banks\PagBrasil\DTO\Response\Payout\Payee;
use SistemAtc\Banks\PagBrasil\Endpoints\PixAutomatico\PixAutomaticoMethods;
use SistemAtc\Banks\PagBrasil\Enums\RecurrenceCycle;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

function pbNoKey(bool $sandbox = true): FakePagBrasilIntegration
{
    return new FakePagBrasilIntegration(signatureKey: null, sandbox: $sandbox);
}

it('cria Link de Pagamento e devolve a url_payment do XML', function () {
    Http::fake(['sandbox.pagbrasil.com/api/checkout/add' => Http::response(
        '<?xml version="1.0" encoding="ISO-8859-1"?><request><url_payment>https://pagbrasil.com/l?abc</url_payment></request>'
    )]);

    $link = Bank::PagBrasil->linkPagamento(pbNoKey())->criar([
        'order' => 'PL-1',
        'amount_brl' => '120.00',
        'payment_option' => 'C,X',
        'url_return' => 'https://loja.example/obrigado',
    ]);

    expect($link->urlPayment)->toBe('https://pagbrasil.com/l?abc');
    Http::assertSent(fn (Request $r) => $r['payment_option'] === 'C,X' && $r['order'] === 'PL-1');
});

it('Link de Pagamento sem XML válido é erro', function () {
    Http::fake(['*/api/checkout/add' => Http::response('Invalid amount_brl')]);

    expect(fn () => Bank::PagBrasil->linkPagamento(pbNoKey())->criar(['order' => '1', 'amount_brl' => 'x']))
        ->toThrow(PagBrasilRequestException::class, 'Invalid amount_brl');
});

it('pixAutomatico() da PagBrasil devolve os métodos da PagBrasil', function () {
    expect(Bank::PagBrasil->pixAutomatico(pbNoKey()))->toBeInstanceOf(PixAutomaticoMethods::class);
});

it('Pix Automático: pagamento+consentimento, só consentimento e cobrança recorrente', function () {
    $xml = '<?xml version="1.0" encoding="ISO-8859-1"?><request><order>R1</order><payment_method>X</payment_method>'
        .'<pix_code>000201</pix_code><pix_rec_id>rec123</pix_rec_id><order_status>WP</order_status></request>';

    Http::fake([
        '*/api/order/add' => Http::response($xml),
        '*/api/pix/rec/add' => Http::response($xml),
    ]);

    $pix = Bank::PagBrasil->pixAutomatico(pbNoKey());

    expect($pix->pagamentoComConsentimento(['order' => 'R1', 'pix_rec_cycle' => RecurrenceCycle::Monthly, 'pix_rec_retry' => true])->pixRecId)->toBe('rec123')
        ->and($pix->consentimento(['payer_name' => 'Ana', 'payer_taxid' => '52998224725', 'pix_rec_cycle' => 'monthly'])->pixCode)->toBe('000201')
        ->and($pix->cobrar(['order' => 'R2', 'pix_rec_id' => 'rec123', 'pix_rec_recurrence_date' => new DateTimeImmutable('2026-10-18')])->order)->toBe('R1');

    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/api/order/add') && ($r['pix_rec'] ?? null) === '1'
        && $r['payment_method'] === 'X' && $r['pix_rec_cycle'] === 'monthly' && $r['pix_rec_retry'] === '1');
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/api/pix/rec/add') && $r['payer_taxid'] === '52998224725');
    Http::assertSent(fn (Request $r) => ($r['pix_rec_recurrence_date'] ?? null) === '2026-10-18' && ! isset($r['pix_rec']));
});

it('consulta recorrência aceitando a grafia pic_rec_expiration da doc', function () {
    Http::fake(['*/api/pix/rec/get' => Http::response(
        '<request><pix_rec_id>rec123</pix_rec_id><status>Authorized</status><pix_rec_cycle>monthly</pix_rec_cycle>'
        .'<pic_rec_expiration>2027-12-31</pic_rec_expiration><pix_rec_minimum_amount>10.00</pix_rec_minimum_amount></request>'
    )]);

    $rec = Bank::PagBrasil->pixAutomatico(pbNoKey())->consultarRecorrencia('rec123');

    expect($rec->isAuthorized())->toBeTrue()
        ->and($rec->cycle())->toBe(RecurrenceCycle::Monthly)
        ->and($rec->pixRecExpiration)->toBe('2027-12-31');
});

it('retentativa devolve a resposta crua (formato não documentado)', function () {
    Http::fake(['*/api/pix/rec/retry' => Http::response('Retry scheduled')]);

    $res = Bank::PagBrasil->pixAutomatico(pbNoKey())->retentar('R2');

    expect($res)->toBeInstanceOf(ApiResult::class)->and($res->message)->toBe('Retry scheduled');
});

it('emulador de cobranças só no sandbox', function () {
    Http::fake(['*/mock/pix/automatic/chargeRecurrences' => Http::response('Charges processed.')]);

    expect(Bank::PagBrasil->pixAutomatico(pbNoKey())->emularCobrancas('2026-10-18'))->toBe('Charges processed.');

    expect(fn () => Bank::PagBrasil->pixAutomatico(pbNoKey(sandbox: false))->emularCobrancas('2026-10-18'))
        ->toThrow(BadMethodCallException::class);
});

it('Payout: cadastra/consulta favorecido e envia payout', function () {
    Http::fakeSequence('sandbox.pagbrasil.com/api/payout/')
        ->push(['action' => 'addpayee', 'success' => 'true'])
        ->push('<request><action>getpayee</action><success>true</success><payee><taxid>91051605962</taxid>'
            .'<bank>341</bank><status>1</status><name>José</name></payee></request>')
        ->push(['action' => 'addpayout', 'success' => true, 'id' => '9876']);

    $payout = Bank::PagBrasil->payout(pbNoKey());

    expect($payout->cadastrarFavorecido(['payee_name' => 'José', 'payee_taxid' => '91051605962'])->success)->toBeTrue();

    $payee = $payout->consultarFavorecido('91051605962');
    expect($payee->payee)->toBeInstanceOf(Payee::class)
        ->and($payee->payee?->isApproved())->toBeTrue()
        ->and($payee->payee?->name)->toBe('José');

    expect($payout->enviar('91051605962', 150.0, 'Comissão', '2026-10-01')->id)->toBe('9876');

    Http::assertSent(fn (Request $r) => $r['action'] === 'addpayee' && $r['payee_taxid'] === '91051605962');
    Http::assertSent(fn (Request $r) => $r['action'] === 'addpayout' && $r['payout_amount'] === '150.00'
        && $r['payout_description'] === 'Comissão' && $r['payout_date'] === '2026-10-01');
});

it('Payout com success=false vira exceção com as mensagens de erro', function () {
    Http::fake(['*/api/payout/' => Http::response([
        'action' => 'addpayee', 'success' => 'false', 'error_message' => 'Invalid payee_bank;Invalid payee_zip',
    ])]);

    expect(fn () => Bank::PagBrasil->payout(pbNoKey())->cadastrarFavorecido(['payee_taxid' => '1']))
        ->toThrow(PagBrasilRequestException::class, 'Invalid payee_bank;Invalid payee_zip');
});
