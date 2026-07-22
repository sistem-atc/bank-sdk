<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Payments;

use BadMethodCallException;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Sispag\PagamentoDetalhe;
use SistemAtc\Banks\Itau\DTO\Response\Sispag\PagamentosSispagList;

/**
 * Consulta de pagamentos SISPAG (Cash Management) — lista e detalhe unificados
 * de TODAS as modalidades (Pix, TED, boleto, tributos…).
 *
 *   - listar():    GET /sispag/v1/pagamentos_sispag         (filtros + paginação)
 *   - consultar(): GET /sispag/v1/pagamentos_sispag/{id}    (detalhe polimórfico)
 */
final class PaymentsMethods extends BaseMethods implements PaymentsEndpoint
{
    private const PATH = '/sispag/v1/pagamentos_sispag';

    /**
     * Lista pagamentos SISPAG conforme os filtros (conta_operacao, período,
     * status, modalidade, valor, paginação).
     *
     * @param  array{conta_operacao?: string, data_inicial?: string, data_final?: string, status?: string, page?: int, page_size?: int}  $filtros
     */
    public function listar(array $filtros = []): PagamentosSispagList
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH, query: $filtros);

        return PagamentosSispagList::fromArray($data['data'] ?? $data);
    }

    public function consultar(string $identificador): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH.'/'.rawurlencode($identificador));

        return PagamentoDetalhe::fromArray($data['data'] ?? $data);
    }

    /**
     * Inclusão de pagamento de boleto/conta pela linha digitável. O endpoint de
     * INCLUSÃO de boleto no SISPAG não está no doc fornecido (a API Cash
     * Management documentada cobre inclusão de Pix + consulta). Fica explícito
     * até termos a spec — nunca chutar path de movimentação de dinheiro.
     *
     * @param  array<string, mixed>  $dados
     */
    public function pagarBoleto(array $dados): DTOInterface
    {
        throw new BadMethodCallException(
            'Itaú: inclusão de pagamento de boleto via SISPAG ainda não mapeada '
            .'(o doc Cash Management cobre inclusão de Pix + consulta). '
            .'Use pix()->pagar() pra Pix, ou forneça a spec do endpoint de boleto.'
        );
    }
}
