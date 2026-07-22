<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\AlteracaoBoletoQrCode;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\BoletoQrCodeGerado;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\ListaBoletosLiquidados;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\LocationReservada;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\TituloLiquidado;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\TituloQrCode;
use SistemAtc\Banks\Bradesco\Endpoints\CobrancaQrCode\CobrancaQrCodeMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/**
 * Cobrança com QR Code (boleto híbrido) do Bradesco — a fiação no connector
 * ainda não existe, então instanciamos a classe de método direto.
 */
function authedCobrancaQrCode(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function cobrancaQrCodeMethods(): CobrancaQrCodeMethods
{
    $i = authedCobrancaQrCode();

    return new CobrancaQrCodeMethods(
        HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API),
        $i,
    );
}

/** @param array<string, mixed> $routes */
function fakeCobrancaQrCode(array $routes): void
{
    Http::fake($routes + [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ]);
}

it('registra boleto com QR Code (POST /boleto-hibrido/cobranca-registro/v1/gerarBoleto)', function () {
    fakeCobrancaQrCode([
        '*/boleto-hibrido/cobranca-registro/v1/gerarBoleto' => Http::response([
            'ctitloCobrCdent' => 12345678901,
            'codStatus10' => 1,
            'status10' => 'A Vencer',
            'nomeSacado10' => 'FULANO DE TAL',
            'valMoeda10' => 150.75,
            'dataVencto10' => '31.12.2026',
            'linhaDig10' => '23791234500000150750000000000000012345678901',
            'codBarras10' => '23791234500000150750000000000000012345678901',
            'sFase' => 2,
            'cindcdCobrMisto' => 'S',
            'ialiasAdsaoCta' => 'chave@pix.com.br',
            'iconcPgtoSpi' => 'TXID0001',
            'ilinkGeracQrcd' => '91103935',
            'wqrcdPdraoMercd' => '00020101021226...6304ABCD',
        ]),
    ]);

    $dto = cobrancaQrCodeMethods()->gerarBoleto([
        'registrarTitulo' => 1,
        'codUsuario' => 'APISERVIC',
        'ctitloCobrCdent' => 12345678901,
        'vnmnalTitloCobr' => 15075,
        'cindcdCobrMisto' => 'S',
    ]);

    expect($dto)->toBeInstanceOf(BoletoQrCodeGerado::class)
        ->and($dto->status10)->toBe('A Vencer')
        ->and($dto->sFase)->toBe(2)
        ->and($dto->wqrcdPdraoMercd)->toBe('00020101021226...6304ABCD')
        ->and($dto->iconcPgtoSpi)->toBe('TXID0001')
        ->and($dto->valMoeda10)->toBe(150.75);

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && str_contains($r->url(), '/boleto-hibrido/cobranca-registro/v1/gerarBoleto')
        && $r['registrarTitulo'] === 1);
});

it('reserva o ID Location (POST /boleto-hibrido/cobranca-reserva-location/v1/reservarLoc)', function () {
    fakeCobrancaQrCode([
        '*/boleto-hibrido/cobranca-reserva-location/v1/reservarLoc' => Http::response([
            'criacao' => '2025-01-08T15:33:56.237Z',
            'id' => '91103935',
            'location' => 'bradesco.com.br/qr',
            'tipoCob' => 'cobv',
        ]),
    ]);

    $dto = cobrancaQrCodeMethods()->reservarLocation([
        'codUsuario' => 'APISERVIC',
        'cnpjCpfBnf' => 68542653,
        'cflialCnpjCpfBnf' => 1018,
        'cctrlCnpjCpfBnf' => 38,
        'cidtfdProdCobr' => 9,
        'agenciaCobr' => 3861,
        'contaCobr' => 41000,
    ]);

    expect($dto)->toBeInstanceOf(LocationReservada::class)
        ->and($dto->id)->toBe('91103935')
        ->and($dto->tipoCob)->toBe('cobv')
        ->and($dto->location)->toBe('bradesco.com.br/qr');
});

it('altera boleto com QR Code e envia o header txId', function () {
    fakeCobrancaQrCode([
        '*/boleto-hibrido/cobranca-alteracao/v1/alteraBoletoConsulta' => Http::response([
            'codigo' => 'CBTT0445',
            'mensagem' => 'ALTERACAO EFETUADA',
        ]),
    ]);

    $dto = cobrancaQrCodeMethods()->alterar(['nossoNumero' => 12345678901], txId: 'TX-1');

    expect($dto)->toBeInstanceOf(AlteracaoBoletoQrCode::class)
        ->and($dto->codigo)->toBe('CBTT0445')
        ->and($dto->mensagem)->toBe('ALTERACAO EFETUADA');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto-hibrido/cobranca-alteracao/v1/alteraBoletoConsulta')
        && $r->hasHeader('txId', 'TX-1'));
});

it('consulta título e traz a 2ª via com QR Code em base64 (POST .../cobranca-consulta-titulo/v1/consultar)', function () {
    fakeCobrancaQrCode([
        '*/boleto-hibrido/cobranca-consulta-titulo/v1/consultar' => Http::response([
            'codMensagem' => 'CBTT0000',
            'codStatus' => 1,
            'status' => 'A VENCER',
            'nomeSacado' => 'FULANO DE TAL',
            'dataVencto' => '11072025',
            'valMoeda' => 150.75,
            'linhaDig' => '23791234500000150750000000000000012345678901',
            'base64' => 'YQdvWvCNW+GPxBh',
            'semvQrcode' => '00020101021226...6304ABCD',
            'schavePix' => 'chave@pix.com.br',
            'cnpjCpfCedente' => 68542653101838,
        ]),
    ]);

    $dto = cobrancaQrCodeMethods()->consultar([
        'cpfCnpjUsuario' => 68542653,
        'filialCnpjUsuario' => 1018,
        'controleCpfCnpjUsuario' => 38,
        'idProduto' => 9,
        'nossoNumero' => 12345678901,
    ]);

    expect($dto)->toBeInstanceOf(TituloQrCode::class)
        ->and($dto->status)->toBe('A VENCER')
        ->and($dto->base64)->toBe('YQdvWvCNW+GPxBh')
        ->and($dto->semvQrcode)->toBe('00020101021226...6304ABCD')
        ->and($dto->codStatus)->toBe(1);
});

it('lista boletos liquidados hidratando a lista de títulos (POST .../cobranca-lista/v1/listar)', function () {
    fakeCobrancaQrCode([
        '*/boleto-hibrido/cobranca-lista/v1/listar' => Http::response([
            'status' => 200,
            'transacao' => 'CBTTIAGQ',
            'mensagem' => 'CONSULTA EFETUADA',
            'pagina' => 2,
            'indMaisPagina' => 'S',
            'qtdeTitulos' => 3,
            'qtdeOcorr' => 2,
            'vtotTitulos' => 30150,
            'titulos' => [
                [
                    'nossoNumero' => 12345678901,
                    'digitoNossoNumero' => '5',
                    'seuNumero' => 'PED-001',
                    'nomePagador' => 'FULANO DE TAL',
                    'valorTitulo' => 15075,
                    'valorPagamento' => 15075,
                    'indicadorPagoQrCode' => 'S',
                    'txId' => 'TXID0001',
                ],
                [
                    'nossoNumero' => 12345678902,
                    'seuNumero' => 'PED-002',
                    'valorTitulo' => 15075,
                    'indicadorPagoQrCode' => 'N',
                ],
            ],
        ]),
    ]);

    $dto = cobrancaQrCodeMethods()->listarLiquidados([
        'cpfCnpj' => ['cpfCnpj' => '68542653', 'filial' => '1018', 'controle' => '38'],
        'produto' => '09',
        'dataMovimentoDe' => '01072025',
        'dataMovimentoAte' => '31072025',
        'paginaAnterior' => '0',
    ]);

    expect($dto)->toBeInstanceOf(ListaBoletosLiquidados::class)
        ->and($dto->indMaisPagina)->toBe('S')
        ->and($dto->qtdeOcorr)->toBe(2)
        ->and($dto->titulos)->toHaveCount(2)
        ->and($dto->titulos[0])->toBeInstanceOf(TituloLiquidado::class)
        ->and($dto->titulos[0]->txId)->toBe('TXID0001')
        ->and($dto->titulos[0]->indicadorPagoQrCode)->toBe('S')
        ->and($dto->titulos[1]->seuNumero)->toBe('PED-002');
});
