<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\LoteCobranca;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\LoteCobrancaList;

/**
 * Emissão em lote de QR Codes com vencimento — base
 * `/regulatorio-pix/v2/lotecobv`. Um lote recebe um identificador e, uma vez
 * criado, não aceita adição/remoção de cobranças. A criação/alteração (PUT/PATCH)
 * é processada de forma assíncrona (retorno sem corpo).
 */
final class LoteCobrancaVencimentoMethods extends BaseMethods
{
    private const BASE = '/regulatorio-pix/v2/lotecobv';

    /**
     * Cria ou substitui integralmente um lote de cobranças com vencimento
     * (PUT /lotecobv/{id}). Processamento assíncrono — resposta sem corpo.
     *
     * @param array<string, mixed> $dados
     */
    public function criarOuAlterar(int|string $id, array $dados): void
    {
        $this->makeRequest(HttpMethod::PUT, self::BASE.'/'.rawurlencode((string) $id), body: $dados);
    }

    /**
     * Revisa cobranças específicas dentro de um lote (PATCH /lotecobv/{id}).
     * Processamento assíncrono — resposta sem corpo.
     *
     * @param array<string, mixed> $dados
     */
    public function revisar(int|string $id, array $dados): void
    {
        $this->makeRequest(HttpMethod::PATCH, self::BASE.'/'.rawurlencode((string) $id), body: $dados);
    }

    /** Consulta um lote específico de cobranças com vencimento (GET /lotecobv/{id}). */
    public function consultar(int|string $id): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode((string) $id));

        return LoteCobranca::fromArray($data);
    }

    /**
     * Lista lotes de cobranças com vencimento por período/filtros (GET /lotecobv).
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return LoteCobrancaList::fromArray($data);
    }
}
