<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Ted;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Situação de uma TED já enviada, conforme consulta no Bradesco.
 *
 * Origem: GET /transferencia/ted/v1/consulta (schema `ConsultaResponse`).
 *
 * `chaveUnicaTed` é a concatenação do `numeroDocumento` com a `dataOperacao`
 * (ex.: "253417812.08.2024") — é o par usado na própria consulta.
 * `statusMensagem` traz o estado do ciclo: EM PROCESSAMENTO / PROCESSADA /
 * DEVOLVIDA; quando DEVOLVIDA, `codigoDaDevolucao` + `descricaoDaDevolucao`
 * explicam o motivo (ex.: "AG.OU CTA DEST. INVALIDA").
 *
 * Atenção aos tipos: nesta consulta o CNPJ/CPF vem QUEBRADO em três campos
 * numéricos (raiz `cnpjOuCpf*`, `filialCnpj*` e `digitoCnpjOuCpf*`) — diferente
 * da efetivação, que devolve o documento inteiro como string.
 */
final class TedConsulta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $chaveUnicaTed = null,
        public readonly ?int $bancoRemetente = null,
        public readonly ?int $agenciaRemetente = null,
        public readonly ?int $contaRemetenteComDigito = null,
        public readonly ?int $cnpjOuCpfRemetente = null,
        public readonly ?int $filialCnpjDoRemetente = null,
        public readonly ?int $digitoCnpjOuCpfRemetente = null,
        public readonly ?string $nomedoClienteRemetente = null,
        public readonly ?int $bancoDestinatario = null,
        public readonly ?int $agenciaDestinatario = null,
        public readonly ?int $contaDestinatario = null,
        public readonly ?int $cnpjOuCpfDestinatario = null,
        public readonly ?int $filialCnpjDestinatario = null,
        public readonly ?int $digitoCnpjOuCpfDestinatario = null,
        public readonly ?string $nomeClienteDestinatario = null,
        public readonly ?float $valorDaTransferencia = null,
        public readonly ?string $statusMensagem = null,
        public readonly ?int $codigoDaDevolucao = null,
        public readonly ?string $descricaoDaDevolucao = null,
        public readonly ?int $codigoRetorno = null,
        public readonly ?string $codigoErro = null,
        public readonly ?string $codigoMensagem = null,
        public readonly ?string $mensagem = null,
    ) {}
}
