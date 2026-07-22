<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Cnab\DTO;

/**
 * Uma linha (registro) de um arquivo CNAB de retorno, já classificada por
 * tipo, com os campos extraídos posicionalmente pelo layout.
 *
 * Genérico de propósito: o mapa de campos vem do Layout do banco/tipo, então
 * esta DTO serve tanto CNAB240 quanto CNAB400 sem herança por banco.
 */
final class RetornoLine
{
    /**
     * @param  string  $recordType  ex.: 'header_arquivo', 'segmento_t',
     *                              'detalhe', 'trailer' — nomeado pelo Layout.
     * @param  array<string, string>  $fields  campo => valor cru (trim), na ordem
     *                                          do layout.
     * @param  string  $raw  a linha bruta completa, pra auditoria/reprocesso.
     */
    public function __construct(
        public readonly string $recordType,
        public readonly array $fields,
        public readonly string $raw,
    ) {}

    public function field(string $name): ?string
    {
        return $this->fields[$name] ?? null;
    }
}
