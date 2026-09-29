<?php

declare(strict_types=1);

use SistemAtc\Banks\PagBrasil\Support\ParsedResponse;
use SistemAtc\Banks\PagBrasil\Support\Signature;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

/*
 * Todos os casos aqui são os exemplos PUBLICADOS na documentação da
 * PagBrasil (chave 36d5f718…d690). Se um deles quebrar, a regra do HMAC
 * divergiu da da PagBrasil — e toda notificação real seria recusada.
 */

function pagbrasilFixture(string $name): string
{
    return (string) file_get_contents(__DIR__.'/../Fixtures/PagBrasil/'.$name);
}

it('reproduz a assinatura do IPN da doc (order + amount_brl + payment_status + comprimento)', function () {
    expect(Signature::sign(['1234567890', '39.50', 'P'], FakePagBrasilIntegration::DOC_KEY))
        ->toBe('3093a7dffa0c04e74e827d1b52ef514e');
});

it('reproduz a assinatura de cada resposta XML da doc', function (string $fixture, string $expected) {
    $parsed = ParsedResponse::parse(pagbrasilFixture($fixture));

    expect($parsed->isXml())->toBeTrue()
        ->and($parsed->data['signature'])->toBe($expected)
        ->and(Signature::matches($expected, Signature::valuesOf($parsed->data), FakePagBrasilIntegration::DOC_KEY))->toBeTrue();
})->with([
    'cartão pago' => ['card-paid.xml', '42927a09be6b369bce73b7a40aa0a9f2'],
    'cartão recusado' => ['card-declined.xml', 'ade1ed7b05831307a4434022b8b1e704'],
    'cartão estornado' => ['card-refunded.xml', '44c0eb7fd06dc698cdf7df2f8beaa2fb'],
    'pix pago' => ['pix-paid.xml', 'df25f0641026b821058de166fe63c448'],
    'boleto pago' => ['boleto-paid.xml', '0c0372d38364f2a6d31ec61b0054ff32'],
    'boleto estornado (Agência)' => ['boleto-refunded.xml', '5045db6353d3970389799acc31710d7f'],
    'débito pago' => ['debit-paid.xml', '9c1fe6cac5521f0b09413ae61e6d1b31'],
    'débito estornado' => ['debit-refunded.xml', '4a6a792aeb1fb2dde3382ef3c3250989'],
]);

it('assina sobre bytes ISO-8859-1: com acento, a fonte UTF-8 NÃO reproduz a doc', function () {
    $parsed = ParsedResponse::parse(pagbrasilFixture('pix-paid.xml'));
    $utf8 = implode('', Signature::valuesOf($parsed->data));

    // 412 bytes em Latin-1 (o que a doc publica) × 414 em UTF-8.
    expect(strlen(mb_convert_encoding($utf8, 'ISO-8859-1', 'UTF-8')))->toBe(412)
        ->and(strlen($utf8))->toBe(414)
        ->and(hash_hmac('md5', $utf8.strlen($utf8), FakePagBrasilIntegration::DOC_KEY))
        ->not->toBe('df25f0641026b821058de166fe63c448');
});

it('reproduz a assinatura da lista de boletos pagos — content byte a byte, com CRLF', function () {
    $content = "<boletos_list>\r\n<boleto>\r\n<order>1234567890</order>\r\n<payment_date>10/15/2010</payment_date>\r\n"
        ."<amount_paid>29.95</amount_paid>\r\n<amount_due>29.95</amount_due>\r\n</boleto>\r\n<boleto>\r\n"
        ."<order>1234567891</order>\r\n<payment_date>10/15/2010</payment_date>\r\n<amount_paid>15.50</amount_paid>\r\n"
        ."<amount_due>16.50</amount_due>\r\n</boleto>\r\n<boleto>\r\n<order>1234567892</order>\r\n"
        ."<payment_date>10/15/2010</payment_date>\r\n<amount_paid>45.00</amount_paid>\r\n<amount_due>35.00</amount_due>\r\n"
        ."<param_url>customer_id=12345%26newsletter=yes</param_url>\r\n</boleto>\r\n</boletos_list>";

    expect(strlen($content))->toBe(555)
        ->and(Signature::sign([$content], FakePagBrasilIntegration::DOC_KEY))->toBe('7bea7c5d998a4cebda5738d59458858e');
});

it('recusa assinatura vazia ou divergente', function () {
    expect(Signature::matches(null, ['a'], 'k'))->toBeFalse()
        ->and(Signature::matches('', ['a'], 'k'))->toBeFalse()
        ->and(Signature::matches(str_repeat('0', 32), ['1234567890', '39.50', 'P'], FakePagBrasilIntegration::DOC_KEY))->toBeFalse()
        ->and(Signature::matches('3093A7DFFA0C04E74E827D1B52EF514E', ['1234567890', '39.50', 'P'], FakePagBrasilIntegration::DOC_KEY))->toBeTrue();
});

it('interpreta os três formatos de resposta da PagBrasil', function () {
    $empty = ParsedResponse::parse(pagbrasilFixture('order-not-found.xml'));
    $json = ParsedResponse::parse('{"url_payment":"https://x"}');
    $text = ParsedResponse::parse("Duplicated order.\n");

    expect($empty->isXml())->toBeTrue()->and($empty->isEmpty())->toBeTrue()
        ->and($json->isJson())->toBeTrue()->and($json->data['url_payment'])->toBe('https://x')
        ->and($text->isText())->toBeTrue()->and($text->text())->toBe('Duplicated order.');
});

it('mantém lista mesmo com um único <item> e decodifica Latin-1 sem declaração', function () {
    $single = ParsedResponse::parse('<?xml version="1.0"?><pagstream><products><item id="0"><sku>A</sku></item></products></pagstream>');
    $latin1 = ParsedResponse::parse(mb_convert_encoding('<request><customer_name>José</customer_name></request>', 'ISO-8859-1', 'UTF-8'));

    expect($single->rootName)->toBe('pagstream')
        ->and($single->data['products'])->toBe([['sku' => 'A']])
        ->and($latin1->data['customer_name'])->toBe('José');
});
