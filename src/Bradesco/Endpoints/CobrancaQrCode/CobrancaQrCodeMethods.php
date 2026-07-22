<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\CobrancaQrCode;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\AlteracaoBoletoQrCode;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\BoletoQrCodeGerado;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\ListaBoletosLiquidados;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\LocationReservada;
use SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode\TituloQrCode;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança com QR Code (boleto híbrido / "Bolecode") do Bradesco: registro do
 * título já com QR Code Pix vinculado, reserva de location, alteração,
 * consulta/2ª via e lista de liquidados.
 *
 * Família OPEN_API (host openapi.bradesco.com.br). Cada operação tem seu
 * próprio microserviço — os base paths NÃO são um prefixo único, por isso cada
 * constante carrega o caminho completo depois do host.
 *
 * O Bradesco usa POST inclusive nas consultas — é assim na spec, não é engano.
 *
 * A BAIXA (`/boleto/cobranca-baixa/v1/baixar`) e o CADASTRO DE WEBHOOK
 * (`/boleto/cobranca-webhook/v1/cadastrar`) do produto híbrido são os MESMOS
 * da Cobrança convencional (paths `/boleto/...`, mesmo request/response) —
 * ficam no produto Cobrança e são compartilhados, não são reimplementados aqui.
 */
final class CobrancaQrCodeMethods extends BaseMethods
{
    /** Registro de boleto com QR Code. */
    private const PATH_REGISTRO = '/boleto-hibrido/cobranca-registro/v1/gerarBoleto';

    /** Reserva do ID Location (QR Code) antes do registro. */
    private const PATH_RESERVA_LOCATION = '/boleto-hibrido/cobranca-reserva-location/v1/reservarLoc';

    /** Alteração de boleto com QR Code. */
    private const PATH_ALTERACAO = '/boleto-hibrido/cobranca-alteracao/v1/alteraBoletoConsulta';

    /** Consulta e 2ª via do título (traz base64 do QR Code, EMV, linha digitável). */
    private const PATH_CONSULTA = '/boleto-hibrido/cobranca-consulta-titulo/v1/consultar';

    /** Lista de boletos liquidados (paginada). */
    private const PATH_LISTA = '/boleto-hibrido/cobranca-lista/v1/listar';

    /**
     * Registra o título já com o QR Code Pix vinculado (boleto híbrido).
     *
     * Campos obrigatórios do payload (entre outros): registrarTitulo,
     * codUsuario, nroCpfCnpjBenef/filCpfCnpjBenef/digCpfCnpjBenef,
     * cpssoaJuridContr, ctpoContrNegoc, nseqContrNegoc, cidtfdProdCobr,
     * cnegocCobr, ctitloCobrCdent (nosso número), demisTitloCobr,
     * dvctoTitloCobr, vnmnalTitloCobr, dados do sacado e `fase` /
     * `cindcdCobrMisto` (indicador de título com QR Code).
     *
     * @param  array<string, mixed>  $dados
     */
    public function gerarBoleto(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_REGISTRO, body: $dados);

        return BoletoQrCodeGerado::fromArray($data);
    }

    /**
     * Reserva um ID Location no BSPI para ser usado no registro do boleto
     * híbrido.
     *
     * @param  array{codUsuario?: string, cnpjCpfBnf?: int, cflialCnpjCpfBnf?: int, cctrlCnpjCpfBnf?: int, cidtfdProdCobr?: int, agenciaCobr?: int, contaCobr?: int}  $dados
     */
    public function reservarLocation(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_RESERVA_LOCATION, body: $dados);

        return LocationReservada::fromArray($data);
    }

    /**
     * Altera um boleto com QR Code já registrado (vencimento, valor, desconto,
     * abatimento, protesto, baixa do QR Code etc.).
     *
     * O `txId` é um header OPCIONAL de rastreio da transação — quando
     * informado, fica no client desta instância.
     *
     * @param  array<string, mixed>  $dados
     */
    public function alterar(array $dados, ?string $txId = null): DTOInterface
    {
        if ($txId !== null) {
            $this->httpClient = $this->httpClient->withHeaders(['txId' => $txId]);
        }

        $data = $this->makeRequest(HttpMethod::POST, self::PATH_ALTERACAO, body: $dados);

        return AlteracaoBoletoQrCode::fromArray($data);
    }

    /**
     * Consulta um título específico / 2ª via — devolve código de barras, linha
     * digitável, EMV do QR Code (`semvQrcode`) e a imagem em `base64`.
     *
     * @param  array{cpfCnpjUsuario?: float|int, filialCnpjUsuario?: int, controleCpfCnpjUsuario?: int, idProduto?: int, contaProduto?: float|int, nossoNumero?: float|int, seqTitulo?: int, status?: int, nomePersonalizado?: string}  $filtros
     */
    public function consultar(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_CONSULTA, body: $filtros);

        return TituloQrCode::fromArray($data);
    }

    /**
     * Lista os boletos liquidados por período de movimento/pagamento.
     *
     * Paginação: na primeira chamada mande `paginaAnterior` = "0"; nas
     * seguintes, repita o valor que veio em `pagina` enquanto
     * `indMaisPagina` = "S".
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listarLiquidados(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_LISTA, body: $filtros);

        return ListaBoletosLiquidados::fromArray($data);
    }
}
