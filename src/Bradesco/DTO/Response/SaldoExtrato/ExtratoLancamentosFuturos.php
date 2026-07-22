<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco de lançamentos futuros (agendados/previstos) do extrato.
 *
 * Origem: GET /v1/fornecimento-extratos-contas/extratos → extratoLancamentosFuturos[]
 */
final class ExtratoLancamentosFuturos implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código de retorno tratado ('0' = sucesso). */
        public readonly ?string $codigoRetorno = null,
        /** Detalhe da mensagem de retorno do backend. */
        public readonly ?string $mensagem = null,
        /** Quantidade de registros retornados (string no contrato). */
        public readonly ?string $quantidadeLancamentos = null,
        /** Grupos de lançamentos futuros. */
        #[ArrayOf(GrupoLancamentosFuturos::class)]
        public readonly array $listaLancamentos = [],
    ) {}

    /**
     * Achata os grupos num único fluxo de lançamentos.
     *
     * @return array<int, Lancamento>
     */
    public function lancamentos(): array
    {
        $lancamentos = [];

        foreach ($this->listaLancamentos as $grupo) {
            $lancamentos = array_merge($lancamentos, $grupo->todos());
        }

        return $lancamentos;
    }
}
