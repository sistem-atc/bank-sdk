<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Cnab;

/**
 * Formato do arquivo CNAB (FEBRABAN). Define o tamanho da linha e o estilo de
 * layout usado pelos parsers/builders.
 *
 *  - CNAB240: layout moderno, 240 posições/linha, estrutura em lotes
 *    (header arquivo → header lote → segmentos → trailer lote → trailer
 *    arquivo). Usado pra pagamentos (PIX/boleto/tributo) e cobrança nova.
 *  - CNAB400: layout legado, 400 posições/linha, sem lotes (header → detalhes
 *    → trailer). Ainda comum em cobrança (retorno de boletos).
 */
enum CnabType: int
{
    case CNAB240 = 240;
    case CNAB400 = 400;

    /** Tamanho fixo de cada linha (sem quebra de linha). */
    public function lineLength(): int
    {
        return $this->value;
    }
}
