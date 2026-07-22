<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\SaqueTroco;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\SaqueTroco\PontoAtendimento;
use SistemAtc\Banks\Itau\DTO\Response\SaqueTroco\PontosAtendimentoList;

/**
 * Gestão dos estabelecimentos comerciais (pontos de atendimento) do produto
 * Pix Saque e Troco do Itaú — base `/saque-troco/v1/pontos-atendimento`.
 *
 * Corresponde ao produto "Saque Troco" (v1.0.7) do portal, que permite
 * cadastrar, consultar, alterar e excluir os pontos que oferecem Pix Saque e
 * Pix Troco em dinheiro em espécie.
 */
final class PontosAtendimentoMethods extends BaseMethods
{
    private const BASE = '/saque-troco/v1/pontos-atendimento';

    /**
     * Recupera os pontos de atendimento (paginado). Filtros opcionais:
     * `pontoAtendimentoId`, `page`, `pageSize`.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return PontosAtendimentoList::fromArray($data['data'] ?? $data);
    }

    /**
     * Cadastra um novo ponto de atendimento.
     *
     * @param  array<string, mixed>  $dados
     */
    public function cadastrar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::BASE, body: $dados);

        return PontoAtendimento::fromArray($data['data'] ?? $data);
    }

    /**
     * Atualiza um ponto de atendimento específico.
     *
     * @param  array<string, mixed>  $dados
     */
    public function atualizar(string $pontoAtendimentoId, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PATCH,
            self::BASE.'/'.rawurlencode($pontoAtendimentoId),
            body: $dados,
        );

        return PontoAtendimento::fromArray($data['data'] ?? $data);
    }

    /**
     * Remove um ponto de atendimento específico.
     *
     * @return array<string, mixed>
     */
    public function excluir(string $pontoAtendimentoId): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            self::BASE.'/'.rawurlencode($pontoAtendimentoId),
        );
    }
}
