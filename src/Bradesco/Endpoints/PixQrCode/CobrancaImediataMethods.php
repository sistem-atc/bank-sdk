<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixQrCode;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Cobranca;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaEmv;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaCobrancas;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança imediata Pix (`cob`) — padrão Bacen, produto "Pix - geração de QR
 * Code" do Bradesco.
 *
 * Cobre `/v2/cob` (criar sem txid, listar), `/v2/cob/{txid}` (criar com txid,
 * revisar, consultar), a variante EMV `/v2/cob-emv` (devolve o payload copia-e-cola
 * e o PNG em base64 já prontos) e a listagem por chave `/v1/cob/chavepix`.
 *
 * FAMÍLIA PIX — host `qrpix.bradesco.com.br` e autorizador `/v2/oauth/token`.
 */
final class CobrancaImediataMethods extends BaseMethods
{
    private const PATH_COB = '/v2/cob';

    private const PATH_COB_EMV = '/v2/cob-emv';

    private const PATH_COB_CHAVE = '/v1/cob/chavepix';

    protected function family(): string
    {
        return BradescoHosts::FAMILY_PIX;
    }

    /**
     * Cria cobrança imediata SEM txid (o Bradesco gera) — POST /v2/cob.
     *
     * @param  array<string, mixed>  $dados  calendario, devedor, valor, chave, solicitacaoPagador, infoAdicionais
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_COB, body: $dados);

        return Cobranca::fromArray($data);
    }

    /**
     * Cria (ou substitui) cobrança imediata COM txid próprio — PUT /v2/cob/{txid}.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criarComTxid(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PUT,
            self::PATH_COB.'/'.rawurlencode($txid),
            body: $dados,
        );

        return Cobranca::fromArray($data);
    }

    /**
     * Revisa (altera parcialmente) a cobrança — PATCH /v2/cob/{txid}.
     * Também é o caminho para remover a cobrança (`status: REMOVIDA_PELO_USUARIO_RECEBEDOR`).
     *
     * @param  array<string, mixed>  $dados
     */
    public function revisar(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PATCH,
            self::PATH_COB.'/'.rawurlencode($txid),
            body: $dados,
        );

        return Cobranca::fromArray($data);
    }

    /** Consulta a cobrança por txid (opcionalmente numa revisão específica) — GET /v2/cob/{txid}. */
    public function consultar(string $txid, ?int $revisao = null): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::PATH_COB.'/'.rawurlencode($txid),
            query: $revisao === null ? [] : ['revisao' => $revisao],
        );

        return Cobranca::fromArray($data);
    }

    /**
     * Lista cobranças imediatas do período — GET /v2/cob.
     *
     * @param  array<string, mixed>  $filtros  inicio e fim são OBRIGATÓRIOS (ISO 8601);
     *                                         opcionais: cpf, cnpj, locationPresente, status,
     *                                         'paginacao.paginaAtual', 'paginacao.itensPorPagina'
     */
    public function listar(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH_COB, query: $filtros);

        return ListaCobrancas::fromArray($data);
    }

    /**
     * Lista cobranças imediatas de UMA chave Pix — GET /v1/cob/chavepix.
     * Mesmos filtros da listagem padrão + `chave` (obrigatória).
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listarPorChave(string $chave, array $filtros): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::PATH_COB_CHAVE,
            query: $filtros + ['chave' => $chave],
        );

        return ListaCobrancas::fromArray($data);
    }

    /**
     * Cria cobrança imediata EMV sem txid — POST /v2/cob-emv.
     * Devolve `emv` (copia-e-cola) e `base64` (PNG do QR) além da cobrança.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criarEmv(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_COB_EMV, body: $dados);

        return CobrancaEmv::fromArray($data);
    }

    /**
     * Cria cobrança imediata EMV com txid próprio — PUT /v2/cob-emv/{txid}.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criarEmvComTxid(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PUT,
            self::PATH_COB_EMV.'/'.rawurlencode($txid),
            body: $dados,
        );

        return CobrancaEmv::fromArray($data);
    }
}
