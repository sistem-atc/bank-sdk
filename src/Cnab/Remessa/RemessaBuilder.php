<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Cnab\Remessa;

use SistemAtc\Banks\Cnab\Layout\LayoutInterface;

/**
 * Monta um arquivo CNAB de remessa (saída) — pagamentos em lote ou registro de
 * cobrança — conforme o Layout injetado.
 *
 * ESQUELETO (camada 1): a montagem completa depende do fieldMap de remessa de
 * cada banco (sequência de registros, dígitos verificadores, formatação de
 * valores/datas). A estrutura de linhas de largura fixa e o wiring do Layout já
 * estão prontos; o preenchimento por segmento entra na camada de CNAB dedicada.
 */
final class RemessaBuilder
{
    public function __construct(
        private readonly LayoutInterface $layout,
    ) {}

    /**
     * Preenche uma linha de largura fixa a partir de um mapa campo => valor,
     * respeitando as posições do layout. Campos não informados ficam em branco;
     * o restante é padded com espaço até o tamanho da linha.
     *
     * @param  array<string, string>  $values
     */
    public function buildLine(string $recordType, array $values): string
    {
        $line = str_repeat(' ', $this->layout->type()->lineLength());

        foreach ($this->layout->fieldMap($recordType) as $name => [$start, $length]) {
            $value = substr(str_pad($values[$name] ?? '', $length), 0, $length);
            $line = substr_replace($line, $value, $start - 1, $length);
        }

        return $line;
    }
}
