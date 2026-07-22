<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Página da lista de títulos baixados.
 * Origem: POST /boleto/cobranca-baixado-consulta/v1/listar
 */
final class TitulosBaixadosResposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código de status HTTP */
        public readonly ?int $status = null,
        /** Fluxo da transação MainFrame */
        public readonly ?string $transacao = null,
        /** Mensagem de retorno */
        public readonly ?string $mensagem = null,
        /** Causa da mensagem de erro no retorno */
        public readonly ?string $causa = null,
        /** Quantidade total de títulos */
        public readonly ?int $qtdeTotalTitulos = null,
        /** Valor total dos títulos que atendem ao critério de pesquisa */
        public readonly ?float $vtotTitulos = null,
        /** Indica se existem mais páginas */
        public readonly ?string $indMaisPagina = null,
        /** Número da página atual */
        public readonly ?int $pagina = null,
        /** Quantidade de ocorrências retornada nessa chamada */
        public readonly ?int $qtdeOcorr = null,
        #[ArrayOf(TituloBaixado::class)] public readonly array $titulos = [],
    ) {}
}
