<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista paginada de agendamentos e pagamentos de boletos de cobrança.
 *
 * Origem: POST /boleto/pagamento-cobranca/v1/cobranca-agendamentos-pgto/listar
 * (schema ListaAgendamentoResponseDTO).
 *
 * Paginação por restart: enquanto `indMaisPagina` indicar mais páginas,
 * reenvie `restartSaida` no campo `restartEntrada` da próxima requisição.
 */
final class ListaAgendamentos implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Agendamento> $agendamentos */
    public function __construct(
        #[ArrayOf(Agendamento::class)]
        public readonly array $agendamentos = [],       // Lista de agendamentos.
        public readonly ?string $causa = null,          // Detalhes da mensagem retornada pelo mainframe.
        public readonly ?string $indMaisPagina = null,  // Indica se há mais páginas para consulta.
        public readonly ?string $mensagem = null,       // Mensagem retornada pelo mainframe.
        public readonly ?int $quantidadePagtosLista = null, // Quantidade de pagamentos da lista.
        public readonly ?string $restartSaida = null,   // Dados de restart p/ a próxima página.
        public readonly ?int $status = null,            // Código de status HTTP.
        public readonly ?int $totalRegistrosLista = null, // Total de registros da lista.
        public readonly ?string $transacao = null,      // Nome da transação do mainframe.
        public readonly ?float $valorTotalPagtosLista = null, // Valor total dos pagamentos da lista.
    ) {}
}
