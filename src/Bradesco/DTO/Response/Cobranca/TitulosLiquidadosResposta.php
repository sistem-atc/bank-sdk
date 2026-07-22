<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Página da lista de títulos liquidados, com os totalizadores de crédito do
 * movimento (pago, oscilação, cheque, dinheiro, diferenças).
 * Origem: POST /boleto/cobranca-lista/v1/listar
 */
final class TitulosLiquidadosResposta implements DTOInterface
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
        /** Causa da mensagem de retorno */
        public readonly ?string $causa = null,
        /** Valor total dos títulos que atendem ao critério de pesquisa */
        public readonly ?int $vtotTitulos = null,
        /** Valor total dos pagamentos */
        public readonly ?int $vtotPag = null,
        /** Valor total de oscilação */
        public readonly ?int $vtotOscila = null,
        /** Sinal do valor total de oscilação */
        public readonly ?string $vtotOscilaS = null,
        /** Valor total dos títulos pagos em cheque */
        public readonly ?int $vtotCheque = null,
        /** Valor total dos títulos pagos em dinheiro */
        public readonly ?int $vtotDinheiro = null,
        /** Valor total da diferença de pagamentos maior que 0 */
        public readonly ?int $difMaior = null,
        /** Valor total da diferença de pagamentos menor que 0 */
        public readonly ?int $difMenor = null,
        /** Sinal valor total da diferença de pagamentos menor que 0 */
        public readonly ?string $difMenorS = null,
        /** Número da página atual */
        public readonly ?int $pagina = null,
        /** Indica se existem mais páginas */
        public readonly ?string $indMaisPagina = null,
        /** Quantidade total de títulos que atendem ao critério de pesquisa */
        public readonly ?int $qtdeTitulos = null,
        /** Quantidade de ocorrências retornada nessa chamada */
        public readonly ?int $qtdeOcorr = null,
        #[ArrayOf(TituloLiquidado::class)] public readonly array $titulos = [],
    ) {}
}
