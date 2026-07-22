<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Agora;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\AlertaVencimentoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\CblcsResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\DadosFinanceirosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\ModeloLiquidacaoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\NomeClienteResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PerfilInvestidorResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Ágora Investimentos — dados cadastrais, liquidação e perfil do investidor.
 *
 * Reúne os microserviços "de uma operação só" do produto (família open_api,
 * todos POST sem corpo, filtros no path):
 *
 *  - managers-cust-access-info           → códigos CBLC do CPF/CNPJ
 *  - managers-cust-aggregated-data-spb   → nome do cliente
 *  - managers-cust-financial-info-update → dados financeiros e bancários
 *  - managers-expiration-alert           → vencimento de cadastro e perfil
 *  - managers-settlement                 → modelo de liquidação
 *  - managers-suitability                → perfil do investidor
 *
 * Somente leitura. Atenção: o CBLC é o "código do investidor" na Ágora e é o
 * mesmo número que os demais grupos chamam de `accountCode`.
 */
final class CadastroMethods extends BaseMethods
{
    private const BASE_ACCESS_INFO = '/managers-cust-access-info/v1';

    private const BASE_AGGREGATED_DATA = '/managers-cust-aggregated-data-spb/v1';

    private const BASE_FINANCIAL_INFO = '/managers-cust-financial-info-update/v1';

    private const BASE_EXPIRATION_ALERT = '/managers-expiration-alert/v1';

    private const BASE_SETTLEMENT = '/managers-settlement/v1';

    private const BASE_SUITABILITY = '/managers-suitability/v1';

    /**
     * Códigos CBLC (código do investidor) vinculados ao CPF/CNPJ.
     *
     * POST /managers-cust-access-info/v1/searchcblc/{cpfCnpj}
     */
    public function cblcs(string $cpfCnpj): CblcsResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE_ACCESS_INFO.'/searchcblc/'.self::seg($cpfCnpj),
        );

        return CblcsResponse::fromArray($data);
    }

    /**
     * Nome completo do cliente no cadastro da Ágora.
     *
     * POST /managers-cust-aggregated-data-spb/v1/clientfulldata/{cpfCnpj}/{accountCode}
     */
    public function nomeCliente(string $cpfCnpj, int|string $accountCode): NomeClienteResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE_AGGREGATED_DATA.'/clientfulldata/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return NomeClienteResponse::fromArray($data);
    }

    /**
     * Dados financeiros e bancários do cadastro (renda, patrimônio e contas —
     * inclusive qual é a conta bancária principal).
     *
     * POST /managers-cust-financial-info-update/v1/FinancialData/{cpfCnpj}
     */
    public function dadosFinanceiros(string $cpfCnpj): DadosFinanceirosResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE_FINANCIAL_INFO.'/FinancialData/'.self::seg($cpfCnpj),
        );

        return DadosFinanceirosResponse::fromArray($data);
    }

    /**
     * Situação cadastral: datas de vencimento do cadastro e do perfil.
     *
     * POST /managers-expiration-alert/v1/expirationAlert/{cpfCnpj}/{cblc}
     */
    public function situacaoCadastral(string $cpfCnpj, int|string $cblc): AlertaVencimentoResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE_EXPIRATION_ALERT.'/expirationAlert/'.self::seg($cpfCnpj).'/'.self::seg($cblc),
        );

        return AlertaVencimentoResponse::fromArray($data);
    }

    /**
     * Modelo de liquidação do cliente (0 = Ágora, 1 = Bradesco).
     *
     * ⚠️ Ordem dos segmentos invertida em relação aos demais: CBLC vem ANTES
     * do CPF, conforme o contrato.
     *
     * POST /managers-settlement/v1/ModelSettlement/{cblc}/{cpf}
     */
    public function modeloLiquidacao(int|string $cblc, string $cpf): ModeloLiquidacaoResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE_SETTLEMENT.'/ModelSettlement/'.self::seg($cblc).'/'.self::seg($cpf),
        );

        return ModeloLiquidacaoResponse::fromArray($data);
    }

    /**
     * Perfil do investidor (suitability), incluindo carteiras administradas e
     * o indicador de fluxo APIC.
     *
     * POST /managers-suitability/v1/CustomerProfile/{cpfCnpj}
     */
    public function perfilInvestidor(string $cpfCnpj): PerfilInvestidorResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE_SUITABILITY.'/CustomerProfile/'.self::seg($cpfCnpj),
        );

        return PerfilInvestidorResponse::fromArray($data);
    }

    /** Escapa um segmento de path. */
    private static function seg(int|string $valor): string
    {
        return rawurlencode((string) $valor);
    }
}
