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
        // `detail`/`title` = RFC 7807 (Inter, APIs Pix do Bacen); `erro.mensagem`
        // = formato antigo da API Banking do Inter.
        $nested = is_array($body['erro'] ?? null) ? $body['erro'] : [];
        $detail = 'HTTP '.$response->status();
        foreach ([
            $body['message'] ?? null,
            $body['error_description'] ?? null,
            $body['error'] ?? null,
            $body['detail'] ?? null,
            $body['title'] ?? null,
            $nested['mensagemDetalhe'] ?? null,
            $nested['mensagem'] ?? null,
        ] as $candidate) {
            if (is_scalar($candidate) && (string) $candidate !== '') {
                $detail = (string) $candidate;
                break;
            }
        }

        // Violações de validação (RFC 7807 do Inter/Bacen): a 1ª diz qual campo.
        $violacao = is_array($body['violacoes'] ?? null) ? ($body['violacoes'][0] ?? null) : null;
        if (is_array($violacao) && isset($violacao['razao']) && is_scalar($violacao['razao'])) {
            $campo = isset($violacao['propriedade']) && is_scalar($violacao['propriedade']) ? $violacao['propriedade'].': ' : '';
            $detail .= ' ('.$campo.$violacao['razao'].')';
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
