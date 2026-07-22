<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Ted;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Retorno da efetivação de uma Transferência Interbancária (TED) do Bradesco.
 *
 * Origem: POST /transferencia/ted/v1/efetiva (schema `EfetivaResponse`).
 *
 * ⚠️ MOVIMENTA DINHEIRO. Campos-chave pra conciliação/idempotência:
 *  - `chaveUnicaParaApi`: chave única da operação no Bradesco, composta por
 *    NÚMERO DO DOCUMENTO + TIMESTAMP (ex.: "27710872024-11-21-11.17.23.259077").
 *    É o identificador que o banco devolve pra rastrear a TED — GUARDE-O.
 *    Os 7 primeiros dígitos são o `numeroDocumento` usado na consulta.
 *  - `codigoIdentificadorDaTransferencia`: identificador definido pelo CLIENTE
 *    e ecoado na resposta (é o que o emissor tem pra amarrar ao próprio registro).
 *  - `codigoDeRetorno` / `codigoDeErro` / `codigoDaMensagem` / `mensagem`:
 *    o Bradesco pode devolver HTTP 200 com erro NEGOCIAL no corpo
 *    (ex.: codigoDaMensagem "TEDB0108" = "OPERACAO EFETUADA COM SUCESSO").
 *    SEMPRE inspecione estes campos antes de considerar a TED efetivada.
 */
final class TedTransferencia implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $origemDaTransferencia = null,
        public readonly ?int $identificadorDoTipoDeTransferencia = null,
        public readonly ?int $bancoRemetente = null,
        public readonly ?int $agenciaRemetente = null,
        public readonly ?int $bancoDestinatario = null,
        public readonly ?int $agenciaDestinatario = null,
        public readonly ?int $contaRemetenteComDigito = null,
        public readonly ?string $tipoContaRemetente = null,
        public readonly ?string $tipoDePessoaRemetente = null,
        public readonly ?string $cnpjOuCpfRemetente = null,
        public readonly ?string $nomeClienteRemetente = null,
        public readonly ?int $contaDestinatario = null,
        public readonly ?string $tipoDeContaDestinatario = null,
        public readonly ?string $tipodePessoaDestinatario = null,
        public readonly ?string $cnpjOuCpfDestinatario = null,
        public readonly ?string $nomeClienteDestinatario = null,
        public readonly ?float $valorDaTransferencia = null,
        public readonly ?int $finalidadeDaTransferencia = null,
        public readonly ?string $codigoIdentificadorDaTransferencia = null,
        public readonly ?string $dataMovimento = null,
        public readonly ?string $tipoDeDoc = null,
        public readonly ?string $tipoDeDocumentoDeBarras = null,
        public readonly ?string $numeroCodigoDeBarras = null,
        public readonly ?int $canalPagamento = null,
        public readonly ?float $valorMulta = null,
        public readonly ?float $valorJuro = null,
        public readonly ?float $valorDescontoOuAbatimento = null,
        public readonly ?float $valorOutrosAcrescimos = null,
        public readonly ?string $indicadorDda = null,
        public readonly ?int $codigoDeRetorno = null,
        public readonly ?string $codigoDeErro = null,
        public readonly ?string $codigoDaMensagem = null,
        public readonly ?string $mensagem = null,
        public readonly ?string $sqlcaDoDb2 = null,
        public readonly ?string $chaveUnicaParaApi = null,
        public readonly ?int $codigoRetornoProgramaRoteador = null,
    ) {}
}
