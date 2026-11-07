<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\LoteCobVConsultado;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\LoteCobVSumario;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\LotesCobVConsultados;

/**
 * Lote de cobranças com vencimento — `/pix/v2/lotecobv`. Escopos
 * `lotecobv.read` / `.write`. Criação e revisão são assíncronas (HTTP 202).
 */
final class LoteCobVMethods extends BaseMethods
{
    use Concerns;

    /** `PUT /lotecobv/{id}`. @param array{descricao: string, cobsv: list<array<string, mixed>>} $dados */
    public function criar(string $id, array $dados): void
    {
        $this->makeRequest(HttpMethod::PUT, '/lotecobv/'.rawurlencode($id), body: $dados);
    }

    /** `PATCH /lotecobv/{id}`. @param array<string, mixed> $dados */
    public function revisar(string $id, array $dados): void
    {
        $this->makeRequest(HttpMethod::PATCH, '/lotecobv/'.rawurlencode($id), body: $dados);
    }

    public function consultar(string $id): LoteCobVConsultado
    {
        return LoteCobVConsultado::fromArray($this->makeRequest(HttpMethod::GET, '/lotecobv/'.rawurlencode($id)));
    }

    public function sumario(string $id): LoteCobVSumario
    {
        return LoteCobVSumario::fromArray($this->makeRequest(HttpMethod::GET, '/lotecobv/'.rawurlencode($id).'/sumario'));
    }

    /** `GET /lotecobv/{id}/situacao/{situacao}` — só as cobranças do lote naquela situação. */
    public function porSituacao(string $id, string $situacao): LoteCobVConsultado
    {
        return LoteCobVConsultado::fromArray(
            $this->makeRequest(HttpMethod::GET, '/lotecobv/'.rawurlencode($id).'/situacao/'.rawurlencode($situacao)),
        );
    }

    /** @param array{paginaAtual?: int, itensPorPagina?: int} $filtros */
    public function listar(string $inicio, string $fim, array $filtros = []): LotesCobVConsultados
    {
        return LotesCobVConsultados::fromArray($this->makeRequest(HttpMethod::GET, '/lotecobv', query: self::periodo($inicio, $fim, $filtros)));
    }
}
