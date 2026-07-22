<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Mapa dos produtos da carteira, keyed pela sigla da classe.
 *
 * Siglas do contrato: rv (renda variavel), tpv/tpb (tesouro), fun (fundos),
 * coe, opc (opcoes), our (ouro), term (termo), cc (conta corrente),
 * gar (garantias), pvd (previdencia).
 *
 * Origem: components.schemas.PortfolioListSummaryProductsApiResponse.
 */
final class CarteiraProdutos implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Renda variavel. */
        public readonly ?CarteiraProduto $rv = null,
        /** Tesouro (venda). */
        public readonly ?CarteiraProduto $tpv = null,
        /** Tesouro (compra). */
        public readonly ?CarteiraProduto $tpb = null,
        /** Fundos de investimento. */
        public readonly ?CarteiraProduto $fun = null,
        /** COE. */
        public readonly ?CarteiraProduto $coe = null,
        /** Opcoes. */
        public readonly ?CarteiraProduto $opc = null,
        /** Ouro. */
        public readonly ?CarteiraProduto $our = null,
        /** Operacoes a termo. */
        public readonly ?CarteiraProduto $term = null,
        /** Conta corrente. */
        public readonly ?CarteiraProduto $cc = null,
        /** Garantias. */
        public readonly ?CarteiraProduto $gar = null,
        /** Previdencia. */
        public readonly ?CarteiraProduto $pvd = null,
    ) {}
}
