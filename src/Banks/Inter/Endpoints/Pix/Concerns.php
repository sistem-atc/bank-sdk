<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

/**
 * Helpers comuns dos grupos de Pix recebimento. A API Pix do Bacen pagina com
 * chave de ponto (`paginacao.paginaAtual`), e o SDK aceita a forma curta.
 */
trait Concerns
{
    /**
     * @param  array<string, mixed>  $filtros
     * @return array<string, mixed>
     */
    private static function periodo(string $inicio, string $fim, array $filtros): array
    {
        foreach (['paginaAtual', 'itensPorPagina'] as $chave) {
            if (array_key_exists($chave, $filtros)) {
                $filtros['paginacao.'.$chave] = $filtros[$chave];
                unset($filtros[$chave]);
            }
        }

        foreach ($filtros as $k => $v) {
            if (is_bool($v)) {
                $filtros[$k] = $v ? 'true' : 'false';
            }
        }

        return array_filter(
            ['inicio' => $inicio, 'fim' => $fim] + $filtros,
            static fn ($v) => $v !== null && $v !== '',
        );
    }
}
