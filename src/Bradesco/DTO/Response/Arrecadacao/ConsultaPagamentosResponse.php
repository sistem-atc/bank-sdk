<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Arrecadacao;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco de retorno da consulta de pagamentos de arrecadação. A API responde uma
 * LISTA destes blocos; cada bloco carrega até 5 registros em `regSaida` e o
 * controle de paginação em `restart`/`contr`.
 *
 * Origem: GET /pagamento/arrecadacao-via-codbarras/v1/{agencia}/{conta}/{tipoConta}
 * (schema `ConsultaPagamentosResponse`).
 */
final class ConsultaPagamentosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $agencia = null,  // Código da Agência [max:5]
        public readonly ?int $banco = null,  // Número do Banco (Fixo> 237) [max:3]
        public readonly ?int $conta = null,  // Número da Conta [max:13]
        public readonly ?int $contaLinkada = null,  // Conta linkada [max:13]
        public readonly ?int $contr = null,  // Número de consultas já efetuadas. Primeiro envio deve ser zeros [ma...
        public readonly ?int $numeroPeriferico = null,  // Número periferico [max:8]
        public readonly ?int $numeroSequencia = null,  // Número sequência [max:4]
        #[ArrayOf(PagamentoResponse::class)] public readonly array $regSaida = [],  // Registros de saída (pagamentos encontrados)
        public readonly ?int $restart = null,  // Controle de Paginação 0 - Não existem mais dados 1 - Existem mais d...
        public readonly ?string $retorno = null,  // Código de Retorno da Execução (ver item 7.3 “Códigos de Retorno da...
        public readonly ?string $sqlCode = null,  // Código de erro retornado pelo gerenciador de banco de dados DB2 [ma...
    ) {}
}
