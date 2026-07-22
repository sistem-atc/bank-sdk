<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Agora;

use DateTimeInterface;
use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\ExtratoFinanceiroResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\ExtratoMargemResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\ExtratoMargemTaxasResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Ágora Investimentos — movimentação financeira (extratos).
 *
 * Microserviço `managers-statement` (família open_api). Consultas por POST sem
 * corpo: CPF/CNPJ, conta e a janela de datas viajam no path. Somente leitura.
 *
 * As datas são `date-time` no contrato; objetos de data são serializados em
 * AAAA-MM-DD e strings passam intactas (o formato exato depende do ambiente).
 */
final class ExtratoMethods extends BaseMethods
{
    private const BASE = '/managers-statement/v1';

    /**
     * Extrato financeiro do cliente na janela informada.
     *
     * POST /managers-statement/v1/financial/{cpfCnpj}/{accountCode}/{startDate}/{endDate}
     */
    public function financeiro(
        string $cpfCnpj,
        int|string $accountCode,
        string|DateTimeInterface $dataInicio,
        string|DateTimeInterface $dataFim,
    ): ExtratoFinanceiroResponse {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/financial/'.self::janela($cpfCnpj, $accountCode, $dataInicio, $dataFim),
        );

        return ExtratoFinanceiroResponse::fromArray($data);
    }

    /**
     * Extrato de uso da margem e do limite do cliente na janela informada.
     *
     * POST /managers-statement/v1/marginlimit/{cpfCnpj}/{accountCode}/{startDate}/{endDate}
     */
    public function margem(
        string $cpfCnpj,
        int|string $accountCode,
        string|DateTimeInterface $dataInicio,
        string|DateTimeInterface $dataFim,
    ): ExtratoMargemResponse {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/marginlimit/'.self::janela($cpfCnpj, $accountCode, $dataInicio, $dataFim),
        );

        return ExtratoMargemResponse::fromArray($data);
    }

    /**
     * Taxas cobradas sobre a margem/limite na janela informada.
     *
     * POST /managers-statement/v1/marginlimit-fees/{cpfCnpj}/{accountCode}/{startDate}/{endDate}
     */
    public function taxasMargem(
        string $cpfCnpj,
        int|string $accountCode,
        string|DateTimeInterface $dataInicio,
        string|DateTimeInterface $dataFim,
    ): ExtratoMargemTaxasResponse {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/marginlimit-fees/'.self::janela($cpfCnpj, $accountCode, $dataInicio, $dataFim),
        );

        return ExtratoMargemTaxasResponse::fromArray($data);
    }

    /** Monta o trecho {cpfCnpj}/{accountCode}/{startDate}/{endDate}. */
    private static function janela(
        string $cpfCnpj,
        int|string $accountCode,
        string|DateTimeInterface $dataInicio,
        string|DateTimeInterface $dataFim,
    ): string {
        return self::seg($cpfCnpj).'/'.self::seg($accountCode)
            .'/'.self::seg(self::data($dataInicio)).'/'.self::seg(self::data($dataFim));
    }

    /** Normaliza a data: objeto vira AAAA-MM-DD, string passa como veio. */
    private static function data(string|DateTimeInterface $data): string
    {
        return $data instanceof DateTimeInterface ? $data->format('Y-m-d') : $data;
    }

    /** Escapa um segmento de path. */
    private static function seg(int|string $valor): string
    {
        return rawurlencode((string) $valor);
    }
}
