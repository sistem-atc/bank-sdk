<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Cobrança com vencimento (`cobv`) — retorno de PUT/PATCH/GET `/v2/cobv/{txid}`.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class CobrancaVencimento implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $txid = null,
        public readonly ?string $status = null,
        public readonly ?int $revisao = null,
        public readonly ?CalendarioVencimento $calendario = null,
        public readonly ?Location $loc = null,
        public readonly ?string $location = null,
        public readonly ?Devedor $devedor = null,
        public readonly ?Recebedor $recebedor = null,
        public readonly ?ValorVencimento $valor = null,
        public readonly ?string $chave = null,
        public readonly ?string $pixCopiaECola = null,
        public readonly ?string $solicitacaoPagador = null,
        #[ArrayOf(InformacaoAdicional::class)] public readonly array $infoAdicionais = [],
        #[ArrayOf(PixRecebido::class)] public readonly array $pix = [],
    ) {}
}
