<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\BoletoNegociado;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\BoletoNegociado\AtivosFinanceirosList;
use SistemAtc\Banks\Itau\DTO\Response\BoletoNegociado\BoletoNegociado;
use SistemAtc\Banks\Itau\DTO\Response\BoletoNegociado\BoletosNegociadosList;

/**
 * API Boletos Negociados / Ativos Financeiros do Itaú (produto `api_boleto_v1`)
 * — base `/boleto/v1`, host `boleto.api.itau.com`. Cobre a consulta rica de
 * boletos (por pagador, situação, datas etc.), a criação de boleto, o detalhe
 * de uma requisição de boleto negociado e a consulta de ativos financeiros
 * (recebíveis) vinculados — insumo da antecipação/cessão.
 */
final class BoletoNegociadoMethods extends BaseMethods
{
    private const BASE = '/boleto/v1';

    /**
     * Consulta boletos por filtros (paginado) — GET /boleto/v1/boletos.
     * Filtros: `idBeneficiario`, `nomePagador`, `cpfPagador`, `cnpjPagador`,
     * `seuNumero`, `nossoNumero`, `situacao`, `dataVencimento`, `dataPagamento`,
     * `page`, `pageSize`, entre outros.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listarBoletos(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/boletos', query: $filtros);

        return BoletosNegociadosList::fromArray([
            'itens' => $data['data'] ?? [],
            'page' => $data['page'] ?? [],
        ]);
    }

    /**
     * Cria um novo boleto — POST /boleto/v1/boletos.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criarBoleto(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::BASE.'/boletos', body: $dados);

        return BoletoNegociado::fromArray($data['data'] ?? $data);
    }

    /**
     * Detalhe de uma requisição de boleto negociado —
     * GET /boleto/v1/requisicoes-boletos-negociados/{requisicaoBoletoNegociadoId}.
     *
     * @return array<string, mixed>  payload cru da requisição
     */
    public function consultarRequisicao(string $requisicaoBoletoNegociadoId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/requisicoes-boletos-negociados/'.rawurlencode($requisicaoBoletoNegociadoId),
        );
    }

    /**
     * Consulta ativos financeiros vinculados a boletos (paginado) —
     * GET /boleto/v1/ativos-financeiros. Filtros: `numeroIdentificacaoBoleto`,
     * `tipoAtivoFinanceiro`, `ativoFinanceiroId`, `page`, `page-size`.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function ativosFinanceiros(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/ativos-financeiros', query: $filtros);

        return AtivosFinanceirosList::fromArray(['itens' => $data['data'] ?? []]);
    }
}
