<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Cobrança imediata (`cob`) — retorno de POST/PUT/PATCH/GET `/v2/cob[/{txid}]`.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class Cobranca implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $txid = null,
        public readonly ?string $status = null,
        public readonly ?int $revisao = null,
        public readonly ?Calendario $calendario = null,
        public readonly ?Location $loc = null,
        public readonly ?string $location = null,
        public readonly ?Devedor $devedor = null,
        public readonly ?Valor $valor = null,
        public readonly ?string $chave = null,
        public readonly ?string $pixCopiaECola = null,
        public readonly ?string $solicitacaoPagador = null,
        #[ArrayOf(InformacaoAdicional::class)] public readonly array $infoAdicionais = [],
        #[ArrayOf(PixRecebido::class)] public readonly array $pix = [],
        public readonly ?int $codCpfCnpj = null,
        public readonly ?int $codFilial = null,
        public readonly ?int $codCtrlCpfCnpj = null,
    ) {}
}
