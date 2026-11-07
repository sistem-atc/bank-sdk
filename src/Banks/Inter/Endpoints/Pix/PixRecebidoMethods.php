<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\Devolucao;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\Pix;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\PixConsultados;

/**
 * Pix RECEBIDOS e devoluções — `/pix/v2/pix`. Escopos `pix.read` /
 * `pix.write`. A devolução MOVIMENTA dinheiro (sai da conta).
 */
final class PixRecebidoMethods extends BaseMethods
{
    use Concerns;

    public function consultar(string $e2eId): Pix
    {
        return Pix::fromArray($this->makeRequest(HttpMethod::GET, '/pix/'.rawurlencode($e2eId)));
    }

    /**
     * `GET /pix` — período em RFC 3339.
     *
     * @param  array{txId?: string, txIdPresente?: bool, devolucaoPresente?: bool, cpf?: string, cnpj?: string, paginaAtual?: int, itensPorPagina?: int}  $filtros
     */
    public function listar(string $inicio, string $fim, array $filtros = []): PixConsultados
    {
        return PixConsultados::fromArray($this->makeRequest(HttpMethod::GET, '/pix', query: self::periodo($inicio, $fim, $filtros)));
    }

    /**
     * `PUT /pix/{e2eId}/devolucao/{id}` — `$id` é o identificador da
     * devolução escolhido pelo recebedor: repetir o mesmo id NÃO devolve 2×.
     *
     * @param  array{valor: string, natureza?: string, descricao?: string}  $dados
     */
    public function devolver(string $e2eId, string $id, array $dados): Devolucao
    {
        return Devolucao::fromArray(
            $this->makeRequest(HttpMethod::PUT, '/pix/'.rawurlencode($e2eId).'/devolucao/'.rawurlencode($id), body: $dados),
        );
    }

    public function consultarDevolucao(string $e2eId, string $id): Devolucao
    {
        return Devolucao::fromArray(
            $this->makeRequest(HttpMethod::GET, '/pix/'.rawurlencode($e2eId).'/devolucao/'.rawurlencode($id)),
        );
    }
}
