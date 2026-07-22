<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Devolucao;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Pix;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\PixList;

/**
 * Consulta de Pix recebidos e devoluções — base `/regulatorio-pix/v2/pix`.
 * Cobre conciliação (consulta individual por e2eid e listagem) e o fluxo de
 * devolução (solicitar/consultar). A devolução no Itaú é síncrona: o PUT já
 * retorna o status final.
 */
final class PixRecebidoMethods extends BaseMethods
{
    private const BASE = '/regulatorio-pix/v2/pix';

    /** Consulta um Pix recebido específico pelo e2eid (GET /pix/{e2eid}). */
    public function consultar(string $e2eid): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($e2eid));

        return Pix::fromArray($data);
    }

    /**
     * Lista Pix recebidos por período/filtros (GET /pix).
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return PixList::fromArray($data);
    }

    /**
     * Solicita a devolução (total ou parcial) de um Pix recebido
     * (PUT /pix/{e2eid}/devolucao/{id}). O `id` é gerado pelo cliente para
     * identificar unicamente a devolução.
     *
     * @param array<string, mixed> $dados
     */
    public function solicitarDevolucao(string $e2eid, string $id, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PUT,
            self::BASE.'/'.rawurlencode($e2eid).'/devolucao/'.rawurlencode($id),
            body: $dados,
        );

        return Devolucao::fromArray($data);
    }

    /** Consulta uma devolução específica (GET /pix/{e2eid}/devolucao/{id}). */
    public function consultarDevolucao(string $e2eid, string $id): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/'.rawurlencode($e2eid).'/devolucao/'.rawurlencode($id),
        );

        return Devolucao::fromArray($data);
    }
}
