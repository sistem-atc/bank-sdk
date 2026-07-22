<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Cobranca;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\ConsultaTituloResposta;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\TitulosBaixadosResposta;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\TitulosLiquidadosResposta;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\TitulosPendentesResposta;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Cobrança Bradesco — consultas: título específico (que também serve de 2ª via)
 * e as três listas de carteira (pendentes, liquidados, baixados).
 *
 * FAMÍLIA: open_api. Um microserviço por consulta:
 *
 *   - consultarTitulo()    POST /boleto/cobranca-consulta/v1/consultar
 *   - listarPendentes()    POST /boleto/cobranca-pendente/v1/listar
 *   - listarLiquidados()   POST /boleto/cobranca-lista/v1/listar
 *   - listarBaixados()     POST /boleto/cobranca-baixado-consulta/v1/listar
 *
 * ⚠️ Todas usam POST — inclusive as consultas. Não "corrija" pra GET.
 *
 * PAGINAÇÃO (listas): a resposta traz `indMaisPagina` ('S' = há mais) e
 * `pagina`. Pra próxima página, reenvie o mesmo filtro com
 * `paginaAnterior` = `pagina` da resposta anterior.
 */
final class CobrancaConsultaMethods extends BaseMethods
{
    private const PATH_CONSULTA = '/boleto/cobranca-consulta/v1/consultar';

    private const PATH_PENDENTES = '/boleto/cobranca-pendente/v1/listar';

    private const PATH_LIQUIDADOS = '/boleto/cobranca-lista/v1/listar';

    private const PATH_BAIXADOS = '/boleto/cobranca-baixado-consulta/v1/listar';

    /**
     * Consulta os dados de um título específico.
     *
     * É ESTE endpoint que alimenta a 2ª VIA do boleto: o bloco `titulo` traz
     * `linhaDig` (linha digitável), `codBarras`, `dataVenctoBol`,
     * `dataLimitePgt` e o cedente/sacado completos — tudo que a impressão
     * precisa. Veja o atalho `segundaVia()`.
     *
     * Corpo: cpfCnpj{cpfCnpj,filial,controle}, produto, negociacao,
     * nossoNumero, sequencia (fixo '0') e status.
     *
     * @param  array<string, mixed>  $filtros  Corpo `RequestDTO` da spec.
     */
    public function consultarTitulo(array $filtros): ConsultaTituloResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_CONSULTA, body: $filtros);

        return ConsultaTituloResposta::fromArray($data['data'] ?? $data);
    }

    /**
     * Atalho de 2ª via: monta o payload mínimo da consulta de título e devolve
     * a mesma resposta de `consultarTitulo()` — de onde se extrai a linha
     * digitável e o código de barras pra reimpressão.
     *
     * @param  array{cpfCnpj: string, filial: string, controle: string}  $cpfCnpj  CPF/CNPJ decomposto do beneficiário.
     * @param  array<string, mixed>  $extras  Campos adicionais (ex.: ['status' => '0']).
     */
    public function segundaVia(
        array $cpfCnpj,
        string $produto,
        string $negociacao,
        string $nossoNumero,
        string $sequencia = '0',
        array $extras = [],
    ): ConsultaTituloResposta {
        return $this->consultarTitulo(array_merge([
            'cpfCnpj' => $cpfCnpj,
            'produto' => $produto,
            'negociacao' => $negociacao,
            'nossoNumero' => $nossoNumero,
            'sequencia' => $sequencia,
        ], $extras));
    }

    /**
     * Lista os títulos PENDENTES DE LIQUIDAÇÃO (em aberto na carteira).
     *
     * Filtros: cpfCnpj{...}, produto, negociacao, nossoNumero,
     * cpfCnpjPagador{...}, dataVencimentoDe/Ate, dataRegistroDe/Ate,
     * valorTituloDe, faixaVencto e paginaAnterior.
     *
     * @param  array<string, mixed>  $filtros  Corpo `RequestDTO` da spec.
     */
    public function listarPendentes(array $filtros): TitulosPendentesResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_PENDENTES, body: $filtros);

        return TitulosPendentesResposta::fromArray($data['data'] ?? $data);
    }

    /**
     * Lista os títulos LIQUIDADOS (pagos) no período.
     *
     * Filtros: cpfCnpj{...}, produto, negociacao, dataMovimentoDe/Ate,
     * dataPagamentoDe/Ate, origemPagamento, valorTituloDe/Ate e paginaAnterior.
     * A resposta traz os totalizadores do movimento (vtotPag, vtotCheque,
     * vtotDinheiro, difMaior/difMenor…).
     *
     * @param  array<string, mixed>  $filtros  Corpo `RequestDTO` da spec.
     */
    public function listarLiquidados(array $filtros): TitulosLiquidadosResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_LIQUIDADOS, body: $filtros);

        return TitulosLiquidadosResposta::fromArray($data['data'] ?? $data);
    }

    /**
     * Lista os títulos BAIXADOS da carteira.
     *
     * Filtros: versao, cpfCnpj{...}, produto, negociacao, dataVencimentoDe/Ate
     * (formato AAAAMMDD, numérico), valorTituloInicio, codigoBaixa e
     * paginaAnterior.
     *
     * @param  array<string, mixed>  $filtros  Corpo `RequestDTO` da spec.
     */
    public function listarBaixados(array $filtros): TitulosBaixadosResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_BAIXADOS, body: $filtros);

        return TitulosBaixadosResposta::fromArray($data['data'] ?? $data);
    }
}
