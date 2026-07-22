<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Ted;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Ted\TedConsulta;
use SistemAtc\Banks\Bradesco\DTO\Response\Ted\TedTransferencia;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Transferência Interbancária TED do Bradesco.
 *
 * Família OPEN_API (server `https://openapi.bradesco.com.br:443/transferencia/ted/v1`;
 * sandbox `openapisandbox.prebanco.com.br`) — o factory já resolve o host, aqui
 * ficam só os paths completos.
 *
 * ⚠️ ESTE GRUPO MOVIMENTA DINHEIRO REAL.
 *
 * IDEMPOTÊNCIA — a spec NÃO define header `Idempotency-Key` nem equivalente.
 * O controle é por CAMPOS DE NEGÓCIO no corpo do `/efetiva`:
 *  - `numeroControle`  — número de controle da operação, definido pelo cliente;
 *  - `codigoIdentificadorDaTransferencia` — código identificador da
 *    transferência, definido pelo cliente e ecoado na resposta.
 * A resposta traz `chaveUnicaParaApi` (NÚMERO DO DOCUMENTO + TIMESTAMP), que é
 * a chave do banco pra rastrear a TED. PERSISTA-A antes de qualquer retry:
 * repetir o POST sem checar antes no `/consulta` pode gerar TED DUPLICADA.
 * Em falha de rede/timeout, o caminho seguro é consultar (numeroDocumento =
 * os 7 primeiros dígitos da chaveUnicaParaApi + dataOperacao) e só reenviar se
 * a consulta não achar a operação.
 */
final class TedMethods extends BaseMethods
{
    /** POST — efetivar a transferência. */
    private const PATH_EFETIVA = '/transferencia/ted/v1/efetiva';

    /** GET — consultar TEDs enviadas. */
    private const PATH_CONSULTA = '/transferencia/ted/v1/consulta';

    /**
     * Efetiva uma Transferência Interbancária (TED).
     *
     * Campos do corpo (schema `EfetivaRequest` — todos opcionais no schema, mas
     * na prática os do bloco remetente/destinatário/valor são obrigatórios):
     *
     * REMETENTE (conta debitada)
     *  - `agenciaRemetente` int, `contaRemetenteComDigito` int (conta COM dígito)
     *  - `tipoContaRemetente` string: CC = corrente, PP = poupança
     *  - `tipoDePessoaRemetente` string: F = física, J = jurídica
     *  - `numeroFilial` string (ex.: "0002")
     *
     * DESTINATÁRIO (favorecido)
     *  - `bancoDestinatario` int (código do banco, ex.: 341)
     *  - `agenciaDestinatario` int, `contaDestinatario` int
     *  - `tipoDeContaDestinatario` string: CC | PP
     *  - `tipodePessoaDestinatario` string: F | J  (atenção ao "d" minúsculo — é assim na spec)
     *  - `numeroInscricao` string: CNPJ ou CPF do destinatário
     *  - `nomeClienteDestinatario` string
     *
     * OPERAÇÃO
     *  - `valorDaTransferencia` float (ex.: 1000.8)
     *  - `finalidadeDaTransferencia` int (código de finalidade, ex.: 10)
     *  - `identificadorDoTipoDeTransferencia` int:
     *      1 = mesma/diferente titularidade, 3 = IF para cliente,
     *      12 = conta salário, 16 = pagamento de boleto
     *  - `tipoDeDoc` string: D = mesma titularidade, E = diferente titularidade
     *  - `dataMovimento` string no formato DD.MM.AAAA (ex.: "21.11.2024")
     *  - `canalPagamento` int, `indicadorDda` string (S/N)
     *  - `numeroControle` string e `codigoIdentificadorDaTransferencia` string
     *    — identificadores do cliente (ver nota de idempotência da classe)
     *
     * SÓ PARA BOLETO (identificadorDoTipoDeTransferencia = 16)
     *  - `tipoDeDocumentoDeBarras`, `numeroCodigoDeBarras`, `valorMulta`,
     *    `valorJuro`, `valorDescontoOuAbatimento`, `valorOutrosAcrescimos`
     *
     * ⚠️ Mesmo com HTTP 200 a TED pode ter sido recusada: cheque
     * `codigoDeErro`, `codigoDeRetorno`, `codigoDaMensagem` e `mensagem` do DTO.
     *
     * @param  array<string, mixed>  $dados  corpo conforme `EfetivaRequest`
     */
    public function efetivar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_EFETIVA, body: $dados);

        return TedTransferencia::fromArray($data['data'] ?? $data);
    }

    /**
     * Consulta uma TED enviada. Ambos os parâmetros são obrigatórios e viajam
     * na query string (aqui o Bradesco usa GET mesmo).
     *
     * @param  int  $numeroDocumento  número do documento da TED (máx. 7 dígitos)
     * @param  string  $dataOperacao  data da operação no formato DD.MM.AAAA (ex.: "12.08.2024")
     */
    public function consultar(int $numeroDocumento, string $dataOperacao): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH_CONSULTA, query: [
            'numeroDocumento' => $numeroDocumento,
            'dataOperacao' => $dataOperacao,
        ]);

        return TedConsulta::fromArray($data['data'] ?? $data);
    }
}
