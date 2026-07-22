<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixQrCode;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Devolucao;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaPixRecebidos;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\PixRecebido;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Gerenciamento de Pix RECEBIDOS e suas devoluções — padrão Bacen.
 *
 * Cobre `/v2/pix` (listar recebidos), `/v2/pix/{e2eid}` (consultar um) e
 * `/v2/pix/{e2eid}/devolucao/{id}` (solicitar e consultar devolução).
 *
 * A devolução MOVIMENTA dinheiro (estorno ao pagador) e é idempotente pelo
 * `{id}` que VOCÊ escolhe — repetir o mesmo id não devolve duas vezes.
 *
 * FAMÍLIA PIX — host `qrpix.bradesco.com.br` e autorizador `/v2/oauth/token`.
 */
final class PixRecebidoMethods extends BaseMethods
{
    private const PATH = '/v2/pix';

    protected function family(): string
    {
        return BradescoHosts::FAMILY_PIX;
    }

    /**
     * Lista Pix recebidos no período — GET /v2/pix.
     *
     * @param  array<string, mixed>  $filtros  inicio e fim OBRIGATÓRIOS; opcionais:
     *                                         txid, txIdPresente, devolucaoPresente,
     *                                         cpf, cnpj, 'paginacao.paginaAtual',
     *                                         'paginacao.itensPorPagina'
     */
    public function listar(array $filtros): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH, query: $filtros);

        return ListaPixRecebidos::fromArray($data);
    }

    /** Consulta um Pix recebido pelo EndToEndId — GET /v2/pix/{e2eid}. */
    public function consultar(string $e2eid): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH.'/'.rawurlencode($e2eid));

        return PixRecebido::fromArray($data);
    }

    /**
     * Solicita a devolução (total ou parcial) de um Pix recebido —
     * PUT /v2/pix/{e2eid}/devolucao/{id}.
     *
     * @param  string  $id  identificador da devolução escolhido pelo recebedor (idempotência)
     * @param  array{valor?: string, natureza?: string, descricao?: string}  $dados
     */
    public function solicitarDevolucao(string $e2eid, string $id, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PUT,
            self::PATH.'/'.rawurlencode($e2eid).'/devolucao/'.rawurlencode($id),
            body: $dados,
        );

        return Devolucao::fromArray($data);
    }

    /** Consulta uma devolução já solicitada — GET /v2/pix/{e2eid}/devolucao/{id}. */
    public function consultarDevolucao(string $e2eid, string $id): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::PATH.'/'.rawurlencode($e2eid).'/devolucao/'.rawurlencode($id),
        );

        return Devolucao::fromArray($data);
    }
}
