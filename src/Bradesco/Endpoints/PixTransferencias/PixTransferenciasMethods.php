<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixTransferencias;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias\SolicitarTransferenciaResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias\TransferenciaResponse;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;

/**
 * Pix - transferências (SPI) do Bradesco.
 *
 * ⚠️ MOVIMENTA DINHEIRO. Família PIX (host qrpix.bradesco.com.br, autorizador
 * /v2/oauth/token) — daí o override de `family()`.
 *
 * Base paths (um microserviço por operação, conforme o `servers` de cada spec):
 *   - POST https://qrpix.bradesco.com.br/v1/spi/solicitar-transferencia
 *   - GET  https://qrpix.bradesco.com.br/v1/spi/consulta/transferencia/{id-transacao}
 *   - GET  https://qrpix.bradesco.com.br/v1/spi/consulta/transferencia/{id-transacao}/{e2e}
 *
 * IDEMPOTÊNCIA: o identificador é o **id da transação (TXID)**, atribuído pela
 * empresa pagadora no corpo da solicitação. A spec é explícita: "O parâmetro
 * `id-transacao` é atribuído pela empresa (pagador) para identificar a
 * transferência e **não poderá ser reutilizado em outra transação**". Não há
 * header de idempotência. Portanto:
 *   - reenvio do MESMO pagamento = mesmo idTransacao (nunca gerar outro);
 *   - status EM_PROCESSAMENTO NÃO é falha — consulte pelo idTransacao
 *     (recomendação da spec: em até 1 minuto) antes de qualquer nova tentativa.
 */
final class PixTransferenciasMethods extends BaseMethods implements PixEndpoint
{
    /** Base path do server da spec "Pix Transferência - Transferir". */
    private const PATH_SOLICITAR = '/v1/spi/solicitar-transferencia';

    /** Base path do server da spec "Pix Transferência - Consultar". */
    private const PATH_CONSULTA = '/v1/spi/consulta/transferencia';

    /** Família PIX: o token do autorizador open_api não vale aqui (401). */
    protected function family(): string
    {
        return BradescoHosts::FAMILY_PIX;
    }

    /**
     * Solicita uma transferência Pix (débito na conta do pagador).
     *
     * POST /v1/spi/solicitar-transferencia
     *
     * O corpo segue o schema `TransferenciaRequest` da spec — repassado tal e
     * qual, sem invenção de campos:
     *   - `idtransacao` (assim mesmo, minúsculo no REQUEST; a RESPOSTA devolve
     *     `idTransacao`): identificador da transação, [a-zA-Z0-9]{1,35};
     *   - `valor`: string com ponto decimal, sem separador de milhar ("200.00");
     *   - `pagador`: {agencia, conta, cpfCnpj, tipoConta};
     *   - `recebedor`: {nomeFavorecido, tipoChave, chavePix e/ou
     *     banco/agencia/conta/ispb/tipoConta, cpfCnpj};
     *   - `descricao` (até 30), `dataCriacao`, `status`, `motivo`.
     *
     * Informando CPF/CNPJ + chave Pix do recebedor, o Bradesco valida se a
     * chave pertence ao CPF/CNPJ; informando só a chave, não valida.
     *
     * HTTP 200 = transação realizada; HTTP 202 = transação rejeitada (o DTO
     * volta com status REJEITADO e `codigoMotivo`).
     *
     * @param  array<string, mixed>  $dados  corpo conforme `TransferenciaRequest`
     */
    public function solicitar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_SOLICITAR, body: $dados);

        return SolicitarTransferenciaResponse::fromArray($data);
    }

    /**
     * Consulta uma transferência pelo id da transação (TXID).
     *
     * GET /v1/spi/consulta/transferencia/{id-transacao}
     */
    public function consultar(string $idTransacao): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::PATH_CONSULTA.'/'.rawurlencode($idTransacao),
        );

        return TransferenciaResponse::fromArray($data);
    }

    /**
     * Consulta uma transferência pelo id da transação (TXID) + EndToEndId.
     *
     * GET /v1/spi/consulta/transferencia/{id-transacao}/{e2e}
     *
     * É o caminho recomendado pela spec para acompanhar transferências que
     * voltaram EM_PROCESSAMENTO (consultar dentro de 1 minuto).
     */
    public function consultarPorE2e(string $idTransacao, string $e2e): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::GET,
            self::PATH_CONSULTA.'/'.rawurlencode($idTransacao).'/'.rawurlencode($e2e),
        );

        return TransferenciaResponse::fromArray($data);
    }

    /**
     * Alias de `solicitar()` que satisfaz o contrato cross-bank PixEndpoint —
     * permite trocar Bradesco por Itaú sem mudar o código do consumidor.
     *
     * @param  array<string, mixed>  $dados
     */
    public function pagar(array $dados): DTOInterface
    {
        return $this->solicitar($dados);
    }
}
