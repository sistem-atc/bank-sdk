<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Envelope da consulta de título específico / emissão de 2ª via.
 * Origem: POST /boleto/cobranca-consulta/v1/consultar
 */
final class ConsultaTituloResposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código de status da operação. */
        public readonly ?int $status = null,
        /** Identificador único da transação. */
        public readonly ?string $transacao = null,
        /** Mensagem descritiva do resultado. */
        public readonly ?string $mensagem = null,
        /** Causa detalhada do resultado. */
        public readonly ?string $causa = null,
        /** Dados completos do título consultado. */
        public readonly ?TituloDetalhado $titulo = null,
        /** Quantidade de mensagens do boleto. */
        public readonly ?int $quantidadeMensagens = null,
        /** Mensagens livres do boleto. */
        #[ArrayOf(MensagemServico::class)] public readonly array $lista = [],
    ) {}
}
