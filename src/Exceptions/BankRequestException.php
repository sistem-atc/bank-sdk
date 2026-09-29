<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

/**
 * Erro de negócio numa chamada à API do banco (HTTP != 2xx ou corpo com erro
 * lógico). Carrega a Response pra que o host inspecione status/corpo sem o SDK
 * ter que mapear cada código de erro proprietário de cada banco.
 */
class BankRequestException extends RuntimeException
{
    public function __construct(
        private readonly Response $response,
        public readonly string $bank = '',
    ) {
        $body = $response->json();
        $body = is_array($body) ? $body : [];

        // Só texto vira mensagem: há API que manda `error` como OBJETO
        // ({"error": {"code": …}}, ex.: PagStream da PagBrasil) — interpolar
        // isso estourava "Array to string conversion" dentro da exceção.
        $detail = 'HTTP '.$response->status();
        foreach (['message', 'error_description', 'error'] as $key) {
            if (isset($body[$key]) && is_scalar($body[$key]) && (string) $body[$key] !== '') {
                $detail = (string) $body[$key];
                break;
            }
        }

        parent::__construct("[{$bank}] {$detail}", $response->status());
    }

    public function status(): int
    {
        return $this->response->status();
    }

    public function response(): Response
    {
        return $this->response;
    }
}
