<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Exceptions;

use Illuminate\Http\Client\Response;

/**
 * Erro de negócio da PagBrasil. Carrega a Response como a exceção-mãe, mas
 * resolve a mensagem nos dois formatos que a PagBrasil usa:
 *
 *   - API clássica: HTTP 200 com o erro em TEXTO PURO no corpo
 *     ("Duplicated order.", "Order not found.", "Refund must be by bank
 *     transfer"…) — o status HTTP não diz nada, quem diz é o corpo não ser o
 *     XML/JSON esperado. No Payout, JSON/XML com success=false e
 *     error_message (várias mensagens separadas por ";").
 *   - PagStream (API v2): HTTP 4xx/5xx com o envelope
 *     {"error": {"type", "code", "message", "details"}}. Ramifique pelo
 *     `errorCode` (estável, snake_case), nunca pela mensagem.
 */
class PagBrasilRequestException extends BankRequestException
{
    /**
     * @param  array<string, mixed>  $details
     */
    public function __construct(
        Response $response,
        string $detail,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorType = null,
        public readonly array $details = [],
    ) {
        parent::__construct($response, bank: 'pagbrasil');

        $this->message = "[pagbrasil] {$detail}";
    }
}
