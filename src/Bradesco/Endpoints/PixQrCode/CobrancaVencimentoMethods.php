<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixQrCode;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaVencimento;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaVencimentoEmv;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaCobrancasVencimento;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança com vencimento Pix (`cobv`) — padrão Bacen, produto "Pix - geração
 * de QR Code" do Bradesco. É a cobrança com data de vencimento, multa, juros,
 * desconto e abatimento (o "boleto Pix").
 *
 * Cobre `/v2/cobv` (listar), `/v2/cobv/{txid}` (criar, revisar, consultar) e a
 * variante EMV `/v2/cobv-emv/{txid}`.
 *
 * Não existe POST sem txid aqui — a `cobv` SEMPRE nasce com txid próprio (PUT).
 *
 * FAMÍLIA PIX — host `qrpix.bradesco.com.br` e autorizador `/v2/oauth/token`.
 */
final class CobrancaVencimentoMethods extends BaseMethods
{
    private const PATH_COBV = '/v2/cobv';

    private const PATH_COBV_EMV = '/v2/cobv-emv';

    protected function family(): string
    {
        return BradescoHosts::FAMILY_PIX;
    }

    /**
     * Cria (ou substitui) a cobrança com vencimento — PUT /v2/cobv/{txid}.
     *
     * @param  array<string, mixed>  $dados  chave, calendario (dataDeVencimento,
     *                                       validadeAposVencimento), devedor, valor
     *                                       (multa/juros/desconto/abatimento), loc,
     *                                       solicitacaoPagador, infoAdicionais
     */
    public function criar(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PUT,
            self::PATH_COBV.'/'.rawurlencode($txid),
            body: $dados,
        );

        return CobrancaVencimento::fromArray($data);
    }

    /**
     * Revisa (altera parcialmente) a cobrança com vencimento — PATCH /v2/cobv/{txid}.
     *
     * @param  array<string, mixed>  $dados
     */
    public function revisar(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PATCH,
            self::PATH_COBV.'/'.rawurlencode($txid),
            body: $dados,
        );

        return CobrancaVencimento::fromArray($data);
    }

    /** Consulta a cobrança com vencimento por txid — GET /v2/cobv/{txid}. */
    public function consultar(string $txid, ?int $revisao = null): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::PATH_COBV.'/'.rawurlencode($txid),
            query: $revisao === null ? [] : ['revisao' => $revisao],
        );

        return CobrancaVencimento::fromArray($data);
    }

    /**
     * Lista cobranças com vencimento do período — GET /v2/cobv.
     *
     * @param  array<string, mixed>  $filtros  inicio e fim OBRIGATÓRIOS; opcionais:
     *                                         cpf, cnpj, locationPresente, status,
     *                                         loteCobVId, 'paginacao.paginaAtual',
     *                                         'paginacao.itensPorPagina'
     */
    public function listar(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH_COBV, query: $filtros);

        return ListaCobrancasVencimento::fromArray($data);
    }

    /**
     * Cria a cobrança com vencimento na variante EMV — PUT /v2/cobv-emv/{txid}.
     * Devolve `emv` (copia-e-cola) e `base64` (PNG do QR) além da cobrança.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criarEmv(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PUT,
            self::PATH_COBV_EMV.'/'.rawurlencode($txid),
            body: $dados,
        );

        return CobrancaVencimentoEmv::fromArray($data);
    }
}
