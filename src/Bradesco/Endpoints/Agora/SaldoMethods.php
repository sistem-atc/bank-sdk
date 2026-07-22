<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Agora;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\LimiteOperacionalResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\SaldoDisponivelResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\SaldoGlobalResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\SaldoLimiteMargemResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Agora\SaldoPatrimonioResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Ágora Investimentos — saldos e limites do cliente.
 *
 * Microserviço `managers-balance-check` (família open_api). São CONSULTAS,
 * mas o verbo é POST (padrão do Bradesco) e sem corpo: todos os filtros vão
 * no path. Somente leitura.
 */
final class SaldoMethods extends BaseMethods
{
    private const BASE = '/managers-balance-check/v1';

    /**
     * Saldo disponível do cliente.
     *
     * POST /managers-balance-check/v1/availableBalance/{cpfCnpj}/{accountCode}
     */
    public function disponivel(string $cpfCnpj, int|string $accountCode): SaldoDisponivelResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/availableBalance/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return SaldoDisponivelResponse::fromArray($data);
    }

    /**
     * Saldo do patrimônio (renda variável) do cliente.
     *
     * POST /managers-balance-check/v1/equitiesBalance/{cpfCnpj}/{accountCode}
     */
    public function patrimonio(string $cpfCnpj, int|string $accountCode): SaldoPatrimonioResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/equitiesBalance/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return SaldoPatrimonioResponse::fromArray($data);
    }

    /**
     * Saldo global — engloba todos os saldos e limites do cliente.
     *
     * POST /managers-balance-check/v1/globalBalance/{cpfCnpj}/{accountCode}
     */
    public function global(string $cpfCnpj, int|string $accountCode): SaldoGlobalResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/globalBalance/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return SaldoGlobalResponse::fromArray($data);
    }

    /**
     * Saldo global com a variante `option` do contrato (mesma shape de
     * resposta do saldo global sem opção).
     *
     * POST /managers-balance-check/v1/globalBalance/{cpfCnpj}/{accountCode}/{option}
     */
    public function globalComOpcao(string $cpfCnpj, int|string $accountCode, int|string $option): SaldoGlobalResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/globalBalance/'.self::seg($cpfCnpj).'/'.self::seg($accountCode).'/'.self::seg($option),
        );

        return SaldoGlobalResponse::fromArray($data);
    }

    /**
     * Limite operacional do cliente.
     *
     * POST /managers-balance-check/v1/operationallimit/{cpfCnpj}/{accountCode}
     */
    public function limiteOperacional(string $cpfCnpj, int|string $accountCode): LimiteOperacionalResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/operationallimit/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return LimiteOperacionalResponse::fromArray($data);
    }

    /**
     * Saldo do limite de margem (conta margem, 1ª e 2ª linha).
     *
     * POST /managers-balance-check/v1/marginLimitBalance/{cpfCnpj}/{accountCode}
     */
    public function limiteMargem(string $cpfCnpj, int|string $accountCode): SaldoLimiteMargemResponse
    {
        $data = $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/marginLimitBalance/'.self::seg($cpfCnpj).'/'.self::seg($accountCode),
        );

        return SaldoLimiteMargemResponse::fromArray($data);
    }

    /** Escapa um segmento de path. */
    private static function seg(int|string $valor): string
    {
        return rawurlencode((string) $valor);
    }
}
