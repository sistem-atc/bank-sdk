<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Cobranca;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\CobrancaList;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\QrCode;

/**
 * QR Code imediato (COB) da API regulatória Pix do Bacen — base
 * `/regulatorio-pix/v2/cob`. Cria, altera, cancela e consulta cobranças de
 * pagamento único com expiração. NÃO movimenta dinheiro de saída; apenas
 * gerencia o registro do QR Code de recebimento.
 */
final class CobrancaImediataMethods extends BaseMethods
{
    private const BASE = '/regulatorio-pix/v2/cob';

    /**
     * Cria uma cobrança imediata com o txid gerado pelo próprio Itaú (POST /cob).
     *
     * @param array<string, mixed> $dados
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::BASE, body: $dados);

        return Cobranca::fromArray($data);
    }

    /**
     * Emite uma cobrança imediata com txid informado pelo recebedor (PUT /cob/{txid}).
     *
     * @param array<string, mixed> $dados
     */
    public function criarComTxid(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PUT, self::BASE.'/'.rawurlencode($txid), body: $dados);

        return Cobranca::fromArray($data);
    }

    /**
     * Altera ou cancela uma cobrança imediata (PATCH /cob/{txid}). Para cancelar,
     * envie `status = REMOVIDO_PELO_USUARIO_RECEBEDOR`.
     *
     * @param array<string, mixed> $dados
     */
    public function revisar(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PATCH, self::BASE.'/'.rawurlencode($txid), body: $dados);

        return Cobranca::fromArray($data);
    }

    /** Consulta uma cobrança imediata específica (GET /cob/{txid}). */
    public function consultar(string $txid): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($txid));

        return Cobranca::fromArray($data);
    }

    /**
     * Lista cobranças imediatas por período/filtros (GET /cob).
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return CobrancaList::fromArray($data);
    }

    /**
     * Obtém a imagem/payload do QR Code de uma cobrança imediata
     * (GET /cob/{txid}/qrcode — endpoint em obsolescência).
     */
    public function obterQrCode(string $txid): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($txid).'/qrcode');

        return QrCode::fromArray($data);
    }
}
