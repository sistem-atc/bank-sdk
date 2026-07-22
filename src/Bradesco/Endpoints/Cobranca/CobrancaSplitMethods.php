<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Cobranca;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\ConsultaSplitResposta;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\ManutencaoSplitResposta;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Cobrança Bradesco — Split Payment (rateio de crédito do título entre
 * beneficiários).
 *
 * FAMÍLIA: open_api. Dois microserviços:
 *
 *   - consultar()  POST /boleto/cobranca-consulta-split/v1/executar
 *   - manutencao() POST /boleto/cobranca-manutencao-split/v1/manutencao-rateio-credito
 *
 * Chaves do título nos dois serviços: cnpjCpf + cflialCnpj + cctrlCnpjCpf
 * (CPF/CNPJ do beneficiário decomposto), idProduto, contaProduto (agência 4 +
 * conta 7, sem dígitos), nossoNumero (sem dígito) e nseqTitulo (valor gerado
 * no registro do título). `nvrsaoLyout` é a versão do layout (1).
 *
 * PAGINAÇÃO da consulta: enquanto `indMaisPagina` = 'S', reenvie o mesmo
 * payload com `restartEntrada` = `restartSaida` da resposta anterior.
 */
final class CobrancaSplitMethods extends BaseMethods
{
    private const PATH_CONSULTA = '/boleto/cobranca-consulta-split/v1/executar';

    private const PATH_MANUTENCAO = '/boleto/cobranca-manutencao-split/v1/manutencao-rateio-credito';

    /**
     * Consulta o rateio de crédito (split payment) configurado num título.
     *
     * @param  array<string, mixed>  $filtros  Corpo `RequestDTO` da spec.
     */
    public function consultar(array $filtros): ConsultaSplitResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_CONSULTA, body: $filtros);

        return ConsultaSplitResposta::fromArray($data['data'] ?? $data);
    }

    /**
     * Mantém (inclui/altera/exclui) as linhas de rateio de crédito de um título.
     *
     * Campos de controle do payload:
     *   - `ccalcRteio`  → sobre qual valor rateia: 1 = valor cobrado,
     *     2 = valor registrado, 3 = menor valor entre os dois.
     *   - `ctpoVlrRteio` → tipo do valor de rateio: 1 = percentual, 2 = valor.
     *   - `canclRteio`  → 'S' cancela o rateio registrado, 'N' mantém.
     *   - `listaRteio[]` → cada linha traz `acaoRteio` (ação sobre aquela
     *     linha), agência/conta do beneficiário (`cagBnefcRteio`,
     *     `cctaBnefcRteio`), `vlrPercRteio`, `ibnefcRteioCredt` e
     *     `floatRteioBnefc`.
     *
     * A resposta devolve a lista de volta com `statusAcaoRteio` e
     * `rmotvoStatusAcao` por linha — é aí que se confere o que o banco aceitou.
     *
     * @param  array<string, mixed>  $dados  Corpo `ManutencaoRateioCreditoRequestDTO` da spec.
     */
    public function manutencao(array $dados): ManutencaoSplitResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_MANUTENCAO, body: $dados);

        return ManutencaoSplitResposta::fromArray($data['data'] ?? $data);
    }
}
