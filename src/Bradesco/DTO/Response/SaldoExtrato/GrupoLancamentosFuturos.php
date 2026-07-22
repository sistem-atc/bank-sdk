<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `listaLancamentos` do bloco `extratoLancamentosFuturos` — a chave é
 * o cabeçalho separador "Lancamentos Futuros".
 *
 * Origem: GET /v1/fornecimento-extratos-contas/extratos
 */
final class GrupoLancamentosFuturos implements DTOInterface
{
    use AutoHydrate {
        fromArray as private autoFromArray;
    }
    use CastToArray;

    public function __construct(
        /** Lançamentos agendados/previstos. */
        #[JsonKey('Lancamentos Futuros')]
        #[ArrayOf(Lancamento::class)]
        public readonly array $lancamentosFuturos = [],
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        $normalizado = [];

        foreach ($data as $chave => $valor) {
            $slug = strtolower(str_replace([' ', '_', '-'], '', (string) $chave));
            $normalizado[$slug === 'lancamentosfuturos' ? 'Lancamentos Futuros' : $chave] = $valor;
        }

        return self::autoFromArray($normalizado);
    }

    /** @return array<int, Lancamento> */
    public function todos(): array
    {
        return $this->lancamentosFuturos;
    }
}
