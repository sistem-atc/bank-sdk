<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * QR Code de cobrança (emissão de QR Code Pix Automático) — resposta de
 * `POST /cobrancas` e `GET /cobrancas/{cobrancaId}`.
 *
 * NOTA: a spec "API - Emissão de QR Code Pix Automático" não publica o schema
 * de request/response dos endpoints /cobrancas (só lista os paths). Os campos
 * abaixo seguem o padrão do arranjo Pix (cob imediata: txid, calendario,
 * valor, chave, devedor, Pix Copia e Cola / location). Ajustar quando o Itaú
 * liberar o contrato detalhado.
 */
final class Cobranca implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Atualizacao> $atualizacao */
    public function __construct(
        public readonly ?string $cobrancaId = null,
        public readonly ?string $txid = null,
        public readonly ?string $status = null,
        public readonly ?string $chave = null,
        public readonly ?string $solicitacaoPagador = null,
        public readonly ?string $pixCopiaECola = null,
        public readonly ?string $location = null,
        public readonly ?Calendario $calendario = null,
        public readonly ?Valor $valor = null,
        public readonly ?Devedor $devedor = null,
        public readonly ?Recebedor $recebedor = null,
        public readonly ?Loc $loc = null,
        #[ArrayOf(Atualizacao::class)]
        public readonly array $atualizacao = [],
    ) {}
}
