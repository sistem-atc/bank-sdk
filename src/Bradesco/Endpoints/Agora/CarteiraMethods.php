<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Agora;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\CarteiraListaResumoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\CarteiraResumoResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Ágora Investimentos — carteira consolidada por classe de ativo.
 *
 * Microserviço `managers-portfolio-mgmt` (família open_api). Consultas por
 * POST sem corpo, filtros no path. Somente leitura.
 */
final class CarteiraMethods extends BaseMethods
{
    private const BASE = '/managers-portfolio-mgmt/v1';

    /**
     * Resumo do investimento consolidado por classe de ativo (alocação +
     * patrimônio bruto total).
     *
     * POST /managers-portfolio-mgmt/v1/summary/{cpfCnpj}/{accountCode}
     */
    public function resumo(string $cpfCnpj, int|string $accountCode): CarteiraResumoResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/summary/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return CarteiraResumoResponse::fromArray($data);
    }

    /**
     * Lista detalhada dos investimentos consolidados por classe de ativo.
     *
     * POST /managers-portfolio-mgmt/v1/listsummary/{cpfCnpj}/{accountCode}
     */
    public function detalhado(string $cpfCnpj, int|string $accountCode): CarteiraListaResumoResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/listsummary/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return CarteiraListaResumoResponse::fromArray($data);
    }

    /**
     * Mesma lista detalhada, porém SEM a parcela de previdência.
     *
     * POST /managers-portfolio-mgmt/v1/listsummaryLessPrev/{cpfCnpj}/{accountCode}
     */
    public function detalhadoSemPrevidencia(string $cpfCnpj, int|string $accountCode): CarteiraListaResumoResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/listsummaryLessPrev/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return CarteiraListaResumoResponse::fromArray($data);
    }

    /** Escapa um segmento de path. */
    private static function seg(int|string $valor): string
    {
        return rawurlencode((string) $valor);
    }
}
