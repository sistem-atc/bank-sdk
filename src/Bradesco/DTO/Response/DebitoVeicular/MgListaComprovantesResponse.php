<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista resumida dos comprovantes de pagamento de um RENAVAM em MG.
 *
 * Origem: POST /v1/debitos-veiculares-mg/lista-comprovantes/listaComprovantesMG
 */
final class MgListaComprovantesResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $quantidadeComprovantes = null,  // ex.: 1
        public readonly ?string $codigoMensagem = null,  // ex.: "LCBR0000"
        public readonly ?string $descricaoMensagem = null,  // ex.: "Operação executada com sucesso."
        #[ArrayOf(MgComprovanteItem::class)] public readonly array $listaComprovantes = [],
    ) {}
}
