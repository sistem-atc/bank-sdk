<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\DTO\Response\Ted\TedConsulta;
use SistemAtc\Banks\Bradesco\DTO\Response\Ted\TedTransferencia;
use SistemAtc\Banks\Bradesco\Endpoints\Ted\TedMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function authedTed(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function tedMethods(): TedMethods
{
    $i = authedTed();

    return new TedMethods(
        HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API),
        $i,
    );
}

function fakeTedTokens(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ];
}

it('efetiva uma TED (POST /transferencia/ted/v1/efetiva) e hidrata a chave unica', function () {
    Http::fake(fakeTedTokens() + [
        '*/transferencia/ted/v1/efetiva*' => Http::response([
            'origemDaTransferencia' => 'APIB',
            'identificadorDoTipoDeTransferencia' => 1,
            'bancoRemetente' => 237,
            'agenciaRemetente' => 2856,
            'bancoDestinatario' => 341,
            'agenciaDestinatario' => 6234,
            'contaRemetenteComDigito' => 500356,
            'tipoContaRemetente' => 'CC',
            'tipoDePessoaRemetente' => 'J',
            'cnpjOuCpfRemetente' => '0051713550000248',
            'nomeClienteRemetente' => 'JOSE DA SILVA',
            'contaDestinatario' => 54754,
            'tipoDeContaDestinatario' => 'CC',
            'tipodePessoaDestinatario' => 'J',
            'cnpjOuCpfDestinatario' => '0051713550000248',
            'nomeClienteDestinatario' => 'MARIA DE SOUZA MATOS',
            'valorDaTransferencia' => 1000.8,
            'finalidadeDaTransferencia' => 10,
            'codigoIdentificadorDaTransferencia' => '25062024',
            'dataMovimento' => '21.11.2024',
            'tipoDeDoc' => 'E',
            'canalPagamento' => 0,
            'indicadorDda' => 'N',
            'codigoDeRetorno' => 0,
            'codigoDaMensagem' => 'TEDB0108',
            'mensagem' => 'OPERACAO EFETUADA COM SUCESSO',
            'chaveUnicaParaApi' => '27710872024-11-21-11.17.23.259077',
        ]),
    ]);

    $res = tedMethods()->efetivar([
        'identificadorDoTipoDeTransferencia' => 1,
        'agenciaRemetente' => 2856,
        'contaRemetenteComDigito' => 500356,
        'tipoContaRemetente' => 'CC',
        'tipoDePessoaRemetente' => 'J',
        'numeroFilial' => '0002',
        'bancoDestinatario' => 341,
        'agenciaDestinatario' => 6234,
        'contaDestinatario' => 54754,
        'tipoDeContaDestinatario' => 'CC',
        'tipodePessoaDestinatario' => 'J',
        'numeroInscricao' => '005171355',
        'nomeClienteDestinatario' => 'MARIA DE SOUZA MATOS',
        'valorDaTransferencia' => 1000.8,
        'finalidadeDaTransferencia' => 10,
        'dataMovimento' => '21.11.2024',
        'tipoDeDoc' => 'E',
        'numeroControle' => '48',
        'codigoIdentificadorDaTransferencia' => '25062024',
    ]);

    expect($res)->toBeInstanceOf(TedTransferencia::class)
        ->and($res->chaveUnicaParaApi)->toBe('27710872024-11-21-11.17.23.259077')
        ->and($res->valorDaTransferencia)->toBe(1000.8)
        ->and($res->bancoDestinatario)->toBe(341)
        ->and($res->agenciaDestinatario)->toBe(6234)
        ->and($res->contaDestinatario)->toBe(54754)
        ->and($res->tipodePessoaDestinatario)->toBe('J')
        ->and($res->finalidadeDaTransferencia)->toBe(10)
        ->and($res->codigoIdentificadorDaTransferencia)->toBe('25062024')
        ->and($res->mensagem)->toBe('OPERACAO EFETUADA COM SUCESSO');
});

it('manda o corpo da TED sem mexer nos campos monetarios e de favorecido', function () {
    Http::fake(fakeTedTokens() + [
        '*/transferencia/ted/v1/efetiva*' => Http::response(['chaveUnicaParaApi' => 'X']),
    ]);

    tedMethods()->efetivar([
        'bancoDestinatario' => 341,
        'agenciaDestinatario' => 6234,
        'contaDestinatario' => 54754,
        'valorDaTransferencia' => 1000.8,
        'finalidadeDaTransferencia' => 10,
        'codigoIdentificadorDaTransferencia' => '25062024',
    ]);

    Http::assertSent(function ($request) {
        if (! str_contains($request->url(), '/transferencia/ted/v1/efetiva')) {
            return false;
        }

        return $request->method() === 'POST'
            && $request['bancoDestinatario'] === 341
            && $request['agenciaDestinatario'] === 6234
            && $request['contaDestinatario'] === 54754
            && $request['valorDaTransferencia'] === 1000.8
            && $request['finalidadeDaTransferencia'] === 10
            && $request['codigoIdentificadorDaTransferencia'] === '25062024';
    });
});

it('consulta uma TED (GET /transferencia/ted/v1/consulta) por numeroDocumento + dataOperacao', function () {
    Http::fake(fakeTedTokens() + [
        '*/transferencia/ted/v1/consulta*' => Http::response([
            'chaveUnicaTed' => '253417812.08.2024',
            'bancoRemetente' => 237,
            'agenciaRemetente' => 2856,
            'contaRemetenteComDigito' => 500356,
            'cnpjOuCpfRemetente' => 5171355,
            'filialCnpjDoRemetente' => 2,
            'digitoCnpjOuCpfRemetente' => 48,
            'nomedoClienteRemetente' => 'CNPJ - EMPRESA - TST GATW AP-013',
            'bancoDestinatario' => 341,
            'agenciaDestinatario' => 6234,
            'contaDestinatario' => 54754,
            'cnpjOuCpfDestinatario' => 5171355,
            'filialCnpjDestinatario' => 2,
            'digitoCnpjOuCpfDestinatario' => 48,
            'nomeClienteDestinatario' => 'MARIA DE SOUZA MATOS',
            'valorDaTransferencia' => 1000.8,
            'statusMensagem' => 'DEVOLVIDA',
            'codigoDaDevolucao' => 2,
            'descricaoDaDevolucao' => 'AG.OU CTA DEST. INVALIDA',
            'codigoRetorno' => 422,
            'codigoErro' => '422',
            'codigoMensagem' => 'TEDB0214',
            'mensagem' => 'VALOR NAO INFORMADO',
        ]),
    ]);

    $res = tedMethods()->consultar(2534178, '12.08.2024');

    expect($res)->toBeInstanceOf(TedConsulta::class)
        ->and($res->chaveUnicaTed)->toBe('253417812.08.2024')
        ->and($res->statusMensagem)->toBe('DEVOLVIDA')
        ->and($res->descricaoDaDevolucao)->toBe('AG.OU CTA DEST. INVALIDA')
        ->and($res->valorDaTransferencia)->toBe(1000.8)
        ->and($res->nomedoClienteRemetente)->toBe('CNPJ - EMPRESA - TST GATW AP-013');

    Http::assertSent(function ($request) {
        if (! str_contains($request->url(), '/transferencia/ted/v1/consulta')) {
            return false;
        }

        return $request->method() === 'GET'
            && str_contains($request->url(), 'numeroDocumento=2534178')
            && str_contains(urldecode($request->url()), 'dataOperacao=12.08.2024');
    });
});
