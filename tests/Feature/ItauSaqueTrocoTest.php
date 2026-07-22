<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Itau\Endpoints\SaqueTroco\PontosAtendimentoMethods;
use SistemAtc\Banks\Itau\Endpoints\SaqueTroco\RemuneracaoMethods;
use SistemAtc\Banks\Itau\DTO\Response\SaqueTroco\PontoAtendimento;
use SistemAtc\Banks\Itau\DTO\Response\SaqueTroco\PontosAtendimentoList;
use SistemAtc\Banks\Itau\DTO\Response\SaqueTroco\RemuneracaoList;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function saqueTrocoAuthed(): FakeBankIntegration
{
    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('lista pontos de atendimento (GET /saque-troco/v1/pontos-atendimento)', function () {
    Http::fake([
        '*/saque-troco/v1/pontos-atendimento*' => Http::response([
            'itens' => [
                ['ponto_atendimento_id' => 'PA1', 'nome' => 'Loja Centro', 'status' => 'ATIVO'],
                ['ponto_atendimento_id' => 'PA2', 'nome' => 'Loja Sul'],
            ],
            'total' => '2',
            'page' => 1,
            'page_size' => 10,
        ]),
    ]);

    $i = saqueTrocoAuthed();
    $m = new PontosAtendimentoMethods(HttpClientFactory::make($i), $i);
    $list = $m->listar(['page' => 1]);

    expect($list)->toBeInstanceOf(PontosAtendimentoList::class)
        ->and($list->itens)->toHaveCount(2)
        ->and($list->itens[0]->pontoAtendimentoId)->toBe('PA1')
        ->and($list->itens[0]->nome)->toBe('Loja Centro')
        ->and($list->total)->toBe('2');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/saque-troco/v1/pontos-atendimento')
        && $r->hasHeader('Authorization', 'Bearer TOK')
        && $r->hasHeader('x-itau-correlationID'));
});

it('cadastra ponto de atendimento (POST /saque-troco/v1/pontos-atendimento)', function () {
    Http::fake([
        '*/saque-troco/v1/pontos-atendimento*' => Http::response([
            'ponto_atendimento_id' => 'PA9',
            'nome' => 'Nova Loja',
            'cnpj' => '12345678000199',
            'status' => 'ATIVO',
        ]),
    ]);

    $i = saqueTrocoAuthed();
    $m = new PontosAtendimentoMethods(HttpClientFactory::make($i), $i);
    $res = $m->cadastrar(['nome' => 'Nova Loja', 'cnpj' => '12345678000199']);

    expect($res)->toBeInstanceOf(PontoAtendimento::class)
        ->and($res->pontoAtendimentoId)->toBe('PA9')
        ->and($res->status)->toBe('ATIVO');

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && str_contains($r->url(), '/saque-troco/v1/pontos-atendimento'));
});

it('atualiza ponto de atendimento (PATCH /pontos-atendimento/{id})', function () {
    Http::fake([
        '*/saque-troco/v1/pontos-atendimento/*' => Http::response([
            'ponto_atendimento_id' => 'PA9',
            'nome' => 'Loja Renomeada',
        ]),
    ]);

    $i = saqueTrocoAuthed();
    $m = new PontosAtendimentoMethods(HttpClientFactory::make($i), $i);
    $res = $m->atualizar('PA9', ['nome' => 'Loja Renomeada']);

    expect($res)->toBeInstanceOf(PontoAtendimento::class)
        ->and($res->nome)->toBe('Loja Renomeada');

    Http::assertSent(fn ($r) => $r->method() === 'PATCH'
        && str_contains($r->url(), '/saque-troco/v1/pontos-atendimento/PA9'));
});

it('exclui ponto de atendimento (DELETE /pontos-atendimento/{id})', function () {
    Http::fake([
        '*/saque-troco/v1/pontos-atendimento/*' => Http::response([]),
    ]);

    $i = saqueTrocoAuthed();
    $m = new PontosAtendimentoMethods(HttpClientFactory::make($i), $i);
    $res = $m->excluir('PA9');

    expect($res)->toBeArray();

    Http::assertSent(fn ($r) => $r->method() === 'DELETE'
        && str_contains($r->url(), '/saque-troco/v1/pontos-atendimento/PA9'));
});

it('consulta remuneracao analitica (GET /remuneracao-analiticos)', function () {
    Http::fake([
        '*/saque-troco/v1/remuneracao-analiticos*' => Http::response([
            'itens' => [
                ['id_conta' => '12345678901', 'cnpj' => '12345678000199', 'valor_remuneracao' => '10.50'],
            ],
            'total' => '1',
        ]),
    ]);

    $i = saqueTrocoAuthed();
    $m = new RemuneracaoMethods(HttpClientFactory::make($i), $i);
    $list = $m->analiticos([
        'idConta' => '12345678901',
        'dataLancamento' => '2023-01-01,2023-01-31',
        'cnpj' => '12345678000199',
    ]);

    expect($list)->toBeInstanceOf(RemuneracaoList::class)
        ->and($list->itens)->toHaveCount(1)
        ->and($list->itens[0]->valorRemuneracao)->toBe('10.50')
        ->and($list->itens[0]->idConta)->toBe('12345678901');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/saque-troco/v1/remuneracao-analiticos')
        && str_contains($r->url(), 'idConta=12345678901'));
});

it('consulta remuneracao consolidada (GET /remuneracao-consolidados)', function () {
    Http::fake([
        '*/saque-troco/v1/remuneracao-consolidados*' => Http::response([
            'itens' => [
                ['id_conta' => '12345678901', 'valor_remuneracao' => '99.00'],
            ],
            'total' => '1',
        ]),
    ]);

    $i = saqueTrocoAuthed();
    $m = new RemuneracaoMethods(HttpClientFactory::make($i), $i);
    $list = $m->consolidados([
        'idConta' => '12345678901',
        'dataLancamento' => '2023-01-01,2023-01-31',
        'cnpj' => '12345678000199',
    ]);

    expect($list)->toBeInstanceOf(RemuneracaoList::class)
        ->and($list->itens[0]->valorRemuneracao)->toBe('99.00');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/saque-troco/v1/remuneracao-consolidados'));
});
