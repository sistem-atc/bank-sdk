<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista paginada de pagamentos devolvidos.
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-lista-pagamento-devolvido/listar
 * (schema ListaDevolvidoResponseDTO).
 *
 * Paginação por restart: enquanto `indMaisPagina` indicar mais páginas,
 * reenvie `restartSaida` no campo `restartEntrada` da próxima requisição.
 */
final class ListaPagamentosDevolvidos implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, PagamentoDevolvido> $listaPagtosDevolvidos */
    public function __construct(
        public readonly ?int $status = null,                       // Código de status HTTP.
        public readonly ?string $transacao = null,                 // Nome da transação do mainframe.
        public readonly ?string $mensagem = null,                  // Mensagem retornada pelo mainframe.
        public readonly ?string $causa = null,                     // Detalhes da mensagem retornada.
        public readonly ?string $restartSaida = null,              // Dados de restart p/ a próxima página.
        public readonly ?string $indMaisPagina = null,             // Indica se há mais páginas para consulta.
        public readonly ?int $quantidadePagtosDevolvidos = null,   // Quantidade de pagamentos devolvidos.
        public readonly ?float $valorTotalPagtosDevolvidos = null, // Valor total dos pagamentos devolvidos.
        public readonly ?int $totalRegistrosLista = null,          // Total de registros da lista.
        #[ArrayOf(PagamentoDevolvido::class)]
        public readonly array $listaPagtosDevolvidos = [],         // Lista de pagamentos devolvidos.
    ) {}
}
