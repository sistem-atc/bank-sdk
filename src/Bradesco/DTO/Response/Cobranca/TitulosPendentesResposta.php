<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Página da lista de títulos pendentes de liquidação. Paginação por
 * `indMaisPagina` = 'S' + reenvio de `paginaAnterior` = `pagina`.
 * Origem: POST /boleto/cobranca-pendente/v1/listar
 */
final class TitulosPendentesResposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null,
        public readonly ?string $transacao = null,
        public readonly ?string $mensagem = null,
        public readonly ?string $causa = null,
        public readonly ?int $pagina = null,
        public readonly ?string $indMaisPagina = null,
        public readonly ?int $qtdeTitulos = null,
        public readonly ?int $vtotTitulos = null,
        public readonly ?int $qtdeOcorr = null,
        #[ArrayOf(TituloPendente::class)] public readonly array $titulos = [],
    ) {}
}
