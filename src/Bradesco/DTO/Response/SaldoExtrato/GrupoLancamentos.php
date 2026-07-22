<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `listaLancamentos` do bloco `extratoUltimosLancamentos`.
 *
 * O Bradesco usa como CHAVE o cabeçalho separador impresso no extrato —
 * "Saldo Anterior", "Ultimos Lancamentos" e "Lancamentos Dia" (a spec grafa
 * este último ora com 'D', ora com 'd'). Normalizamos as variantes antes de
 * hidratar, para o consumidor nunca depender do caixa-alta do banco.
 *
 * Origem: GET /v1/fornecimento-extratos-contas/extratos
 */
final class GrupoLancamentos implements DTOInterface
{
    use AutoHydrate {
        fromArray as private autoFromArray;
    }
    use CastToArray;

    /** Variantes aceitas => chave canônica do contrato. */
    private const ALIASES = [
        'saldoanterior' => 'Saldo Anterior',
        'ultimoslancamentos' => 'Ultimos Lancamentos',
        'ultimoslancamento' => 'Ultimos Lancamentos',
        'lancamentosdia' => 'Lancamentos Dia',
        'lancamentodia' => 'Lancamentos Dia',
    ];

    public function __construct(
        /** Saldo anterior ao último movimento do período pesquisado. */
        #[JsonKey('Saldo Anterior')]
        #[ArrayOf(Lancamento::class)]
        public readonly array $saldoAnterior = [],
        /** Últimos lançamentos (D-1 pra trás). */
        #[JsonKey('Ultimos Lancamentos')]
        #[ArrayOf(Lancamento::class)]
        public readonly array $ultimosLancamentos = [],
        /** Lançamentos do dia corrente. */
        #[JsonKey('Lancamentos Dia')]
        #[ArrayOf(Lancamento::class)]
        public readonly array $lancamentosDia = [],
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        $normalizado = [];

        foreach ($data as $chave => $valor) {
            $slug = strtolower(str_replace([' ', '_', '-'], '', (string) $chave));
            $normalizado[self::ALIASES[$slug] ?? $chave] = $valor;
        }

        return self::autoFromArray($normalizado);
    }

    /**
     * Todos os lançamentos do grupo, na ordem de impressão do extrato.
     *
     * @return array<int, Lancamento>
     */
    public function todos(): array
    {
        return array_merge($this->saldoAnterior, $this->ultimosLancamentos, $this->lancamentosDia);
    }
}
