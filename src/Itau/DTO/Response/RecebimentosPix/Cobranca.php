<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança Pix — recurso comum a QR Code imediato (COB) e com vencimento (COBV).
 * Resposta de `POST /cob`, `PUT|PATCH|GET /cob/{txid}` e `PUT|PATCH|GET /cobv/{txid}`.
 *
 * `status` ∈ {ATIVA, CONCLUIDA, REMOVIDO_PELO_USUARIO_RECEBEDOR, REMOVIDO_PELO_PSP}.
 * `pixCopiaECola` é o payload EMV pronto pra renderizar o QR Code. `pix` lista os
 * pagamentos já efetuados sobre a cobrança. Em COBV o objeto `valor` traz os
 * encargos (multa/juros/desconto/abatimento).
 *
 * @property array<int, InfoAdicional> $infoAdicionais
 * @property array<int, Pix>           $pix
 */
final class Cobranca implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param array<int, InfoAdicional> $infoAdicionais
     * @param array<int, Pix>           $pix
     */
    public function __construct(
        public readonly ?string $txid = null,
        public readonly ?int $revisao = null,
        public readonly ?string $status = null,
        public readonly ?string $chave = null,
        public readonly ?string $solicitacaoPagador = null,
        public readonly ?string $pixCopiaECola = null,
        public readonly ?string $location = null,
        public readonly ?Calendario $calendario = null,
        public readonly ?Location $loc = null,
        public readonly ?Devedor $devedor = null,
        public readonly ?Recebedor $recebedor = null,
        public readonly ?Valor $valor = null,
        #[ArrayOf(InfoAdicional::class)]
        public readonly array $infoAdicionais = [],
        #[ArrayOf(Pix::class)]
        public readonly array $pix = [],
    ) {}
}
