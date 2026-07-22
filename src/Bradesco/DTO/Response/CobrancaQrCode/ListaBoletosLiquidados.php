<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Página da lista de boletos liquidados (com totalizadores e paginação por
 * `paginaAnterior`).
 *
 * Endpoint: POST /boleto-hibrido/cobranca-lista/v1/listar
 */
final class ListaBoletosLiquidados implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $status = null, // Código de status HTTP
        public readonly ?string $transacao = null, // Fluxo da transação MainFrame
        public readonly ?string $mensagem = null, // Mensagem de retorno
        public readonly ?string $causa = null, // Causa da mensagem de retorno
        public readonly ?int $vtotTitulos = null, // Valor total dos títulos que atendem ao critério de pesquisa
        public readonly ?int $vtotPag = null, // Valor total dos pagamentos
        public readonly ?int $vtotOscila = null, // Valor total de oscilação
        public readonly ?string $vtotOscilaS = null, // Sinal do valor total de oscilação
        public readonly ?int $vtotCheque = null, // Valor total dos títulos pagos em cheque
        public readonly ?int $vtotDinheiro = null, // Valor total dos títulos pagos em dinheiro
        public readonly ?int $difMaior = null, // Valor total da diferença de pagamentos maior que 0
        public readonly ?int $difMenor = null, // Valor total da diferença de pagamentos menor que 0
        public readonly ?string $difMenorS = null, // Sinal valor total da diferença de pagamentos menor que 0
        public readonly ?int $pagina = null, // Número da página atual
        public readonly ?string $indMaisPagina = null, // Indica se existem mais páginas
        public readonly ?int $qtdeTitulos = null, // Quantidade total de títulos que atendem ao critério de pesquisa
        public readonly ?int $qtdeOcorr = null, // Quantidade de ocorrências retornada nessa chamada
        /** @var list<TituloLiquidado> */
        #[ArrayOf(TituloLiquidado::class)]
        public readonly array $titulos = [], // Lista de títulos liquidados da página
    ) {}
}
