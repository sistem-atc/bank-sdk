<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Agora;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoAcoesResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoBtcResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoCoeResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoDetalhadaFundosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoDetalhadaTesouroDiretoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoFundosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoFuturosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoOpcoesResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoPrevidenciaResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoRendaFixaResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoTermoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\PosicaoTesouroDiretoResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Ágora Investimentos — posição consolidada e detalhada do cliente.
 *
 * Microserviço `managers-position-mgmt` (família open_api). Diferente do resto
 * do catálogo Bradesco, aqui as consultas são GET mesmo — e todos os filtros
 * viajam no PATH (CPF/CNPJ, código da conta/CBLC, e o que mais o endpoint
 * exigir). API SOMENTE LEITURA: nada aqui movimenta dinheiro.
 *
 * As respostas seguem um envelope comum (`meta`, `statusCode`, `errors`,
 * `response`, `code`, `description`) + a lista da classe de ativo. A ÚNICA
 * exceção é renda fixa detalhada, onde `response` é a própria lista de títulos.
 */
final class PosicaoMethods extends BaseMethods
{
    private const BASE = '/managers-position-mgmt/v1';

    /**
     * Posição consolidada em renda variável (ações).
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/equities/{cpfCnpj}/{accountCode}
     */
    public function acoes(string $cpfCnpj, int|string $accountCode): PosicaoAcoesResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/equities/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoAcoesResponse::fromArray($data);
    }

    /**
     * Posição consolidada em fundos de investimento.
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/funds/{cpfCnpj}/{accountCode}
     */
    public function fundos(string $cpfCnpj, int|string $accountCode): PosicaoFundosResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/funds/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoFundosResponse::fromArray($data);
    }

    /**
     * Posição consolidada em operações a termo.
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/term/{cpfCnpj}/{accountCode}
     */
    public function termo(string $cpfCnpj, int|string $accountCode): PosicaoTermoResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/term/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoTermoResponse::fromArray($data);
    }

    /**
     * Posição consolidada em opções.
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/option/{cpfCnpj}/{accountCode}
     */
    public function opcoes(string $cpfCnpj, int|string $accountCode): PosicaoOpcoesResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/option/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoOpcoesResponse::fromArray($data);
    }

    /**
     * Posição consolidada em contratos futuros.
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/futures/{cpfCnpj}/{accountCode}
     */
    public function futuros(string $cpfCnpj, int|string $accountCode): PosicaoFuturosResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/futures/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoFuturosResponse::fromArray($data);
    }

    /**
     * Posição consolidada em BTC (banco de títulos / aluguel de ativos).
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/btc/{cpfCnpj}/{accountCode}
     */
    public function btc(string $cpfCnpj, int|string $accountCode): PosicaoBtcResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/btc/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoBtcResponse::fromArray($data);
    }

    /**
     * Posição consolidada em COE (Certificado de Operações Estruturadas).
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/coe/{cpfCnpj}/{accountCode}
     */
    public function coe(string $cpfCnpj, int|string $accountCode): PosicaoCoeResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/coe/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoCoeResponse::fromArray($data);
    }

    /**
     * Posição consolidada no Tesouro Direto.
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/treasuryDirect/{cpfCnpj}/{accountCode}
     */
    public function tesouroDireto(string $cpfCnpj, int|string $accountCode): PosicaoTesouroDiretoResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/treasuryDirect/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoTesouroDiretoResponse::fromArray($data);
    }

    /**
     * Posição consolidada de previdência. Único endpoint do grupo que NÃO
     * recebe código de conta — a previdência é do CPF/CNPJ.
     *
     * GET /managers-position-mgmt/v1/consolidatedposition/pension/{cpfCnpj}
     */
    public function previdencia(string $cpfCnpj): PosicaoPrevidenciaResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/consolidatedposition/pension/'.self::seg($cpfCnpj),
        );

        return PosicaoPrevidenciaResponse::fromArray($data);
    }

    /**
     * Posição DETALHADA de renda fixa (título a título).
     *
     * ⚠️ Aqui `response` é a lista de títulos, não o bloco de status.
     *
     * GET /managers-position-mgmt/v1/detailedposition/fixedIncome/{cpfCnpj}/{accountCode}
     */
    public function rendaFixaDetalhada(string $cpfCnpj, int|string $accountCode): PosicaoRendaFixaResponse
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/detailedposition/fixedIncome/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return PosicaoRendaFixaResponse::fromArray($data);
    }

    /**
     * Posição DETALHADA de fundos, por fonte (sourceCode) do cliente.
     *
     * GET /managers-position-mgmt/v1/detailedposition/funds/{cpfCnpj}/{accountCode}/{sourceCode}
     */
    public function fundosDetalhados(
        string $cpfCnpj,
        int|string $accountCode,
        int|string $sourceCode,
    ): PosicaoDetalhadaFundosResponse {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/detailedposition/funds/'.self::seg($cpfCnpj).'/'.self::seg($accountCode).'/'.self::seg($sourceCode),
        );

        return PosicaoDetalhadaFundosResponse::fromArray($data);
    }

    /**
     * Posição DETALHADA do Tesouro Direto, por tipo de título e vencimento.
     *
     * @param  string  $bondType  tipo do título (ex.: 'LTN', 'NTN-B')
     * @param  int|string  $maturityDate  vencimento no formato numérico do contrato (AAAAMMDD)
     *
     * GET /managers-position-mgmt/v1/detailedposition/treasuryDirect/{cpfCnpj}/{accountCode}/{bondType}/{maturityDate}
     */
    public function tesouroDiretoDetalhado(
        string $cpfCnpj,
        int|string $accountCode,
        string $bondType,
        int|string $maturityDate,
    ): PosicaoDetalhadaTesouroDiretoResponse {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/detailedposition/treasuryDirect/'.self::seg($cpfCnpj).'/'.self::seg($accountCode)
                .'/'.self::seg($bondType).'/'.self::seg($maturityDate),
        );

        return PosicaoDetalhadaTesouroDiretoResponse::fromArray($data);
    }

    /** Escapa um segmento de path (os filtros da Ágora viajam na URL). */
    private static function seg(int|string $valor): string
    {
        return rawurlencode((string) $valor);
    }
}
