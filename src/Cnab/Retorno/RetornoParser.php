<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Cnab\Retorno;

use SistemAtc\Banks\Cnab\DTO\RetornoLine;
use SistemAtc\Banks\Cnab\Layout\LayoutInterface;

/**
 * Lê um arquivo CNAB de retorno e devolve suas linhas já classificadas e com
 * os campos extraídos posicionalmente, conforme o Layout injetado.
 *
 * Agnóstico de banco: toda a especificidade (tamanho de linha, tipos de
 * registro, posições) vem do LayoutInterface. Trabalha por posição de BYTE
 * (CNAB é ASCII de largura fixa), então usa as funções `mb_*` desligadas de
 * multibyte não — `substr` cru é o correto aqui.
 */
final class RetornoParser
{
    public function __construct(
        private readonly LayoutInterface $layout,
    ) {}

    /**
     * @param  string  $content  conteúdo bruto do arquivo de retorno.
     * @return array<int, RetornoLine>
     */
    public function parse(string $content): array
    {
        $result = [];

        foreach ($this->splitLines($content) as $line) {
            if ($line === '') {
                continue;
            }

            $recordType = $this->layout->identifyRecordType($line);
            $result[] = new RetornoLine(
                recordType: $recordType,
                fields: $this->extractFields($line, $recordType),
                raw: $line,
            );
        }

        return $result;
    }

    /**
     * Quebra o conteúdo em linhas. Aceita arquivos com quebra (\r\n, \n) e
     * arquivos "colados" de largura fixa (sem quebra), fatiando pelo tamanho da
     * linha do layout.
     *
     * @return array<int, string>
     */
    private function splitLines(string $content): array
    {
        if (str_contains($content, "\n")) {
            return array_map(
                static fn (string $l): string => rtrim($l, "\r\n"),
                explode("\n", $content),
            );
        }

        return str_split($content, $this->layout->type()->lineLength()) ?: [];
    }

    /**
     * @return array<string, string>
     */
    private function extractFields(string $line, string $recordType): array
    {
        $fields = [];

        foreach ($this->layout->fieldMap($recordType) as $name => [$start, $length]) {
            // Posições do manual CNAB são 1-based; substr é 0-based.
            $fields[$name] = trim(substr($line, $start - 1, $length));
        }

        return $fields;
    }
}
