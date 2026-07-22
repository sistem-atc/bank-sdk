<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Sispag;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Detalhe de um pagamento SISPAG — `GET /pagamentos_sispag/{id_pagamento_sispag}`.
 *
 * O payload é POLIMÓRFICO por tipo de pagamento: além dos blocos comuns
 * (`dados_debito`, `dados_pagamento`, `historico_pagamento`), traz UM bloco
 * `dados_*` específico do tipo (`dados_pix_transferencia`, `dados_ted`,
 * `dados_boleto`, `dados_darf`, `dados_gps`, `dados_concessionaria`…). Como o
 * subtipo varia por transação, mantemos esses blocos como arrays crus — o host
 * lê o que precisa sem o SDK ter que modelar as ~20 modalidades. Os comuns
 * ficam tipados.
 *
 * @property array<string, mixed>|null $dadosDebito
 * @property array<string, mixed>|null $dadosPagamento
 * @property list<array<string, mixed>>|null $historicoPagamento
 */
final class PagamentoDetalhe implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<string, mixed>|null  $dadosDebito
     * @param  array<string, mixed>|null  $dadosPagamento
     * @param  list<array<string, mixed>>|null  $historicoPagamento
     */
    public function __construct(
        public readonly ?array $dadosDebito = null,
        public readonly ?array $dadosPagamento = null,
        public readonly ?array $historicoPagamento = null,
    ) {}
}
