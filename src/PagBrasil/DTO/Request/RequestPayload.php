<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request;

/**
 * DTO de ENTRADA da PagBrasil: descreve o conteúdo de uma requisição e sabe
 * se serializar nos nomes de campo da API. Não sabe nada de ambiente,
 * credencial ou transporte — isso é do SDK (host) e da integração (qual
 * ambiente).
 *
 * Os endpoints aceitam o DTO ou, por compatibilidade, o array cru com os
 * nomes da doc.
 */
interface RequestPayload
{
    /**
     * Campos com os nomes da API (snake_case); null é omitido.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(): array;
}
