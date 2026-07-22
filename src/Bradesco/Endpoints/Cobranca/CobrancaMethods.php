<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Cobranca;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\AlteracaoBoletoResposta;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\BaixaTituloResposta;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\BoletoRegistrado;
use SistemAtc\Banks\Bradesco\DTO\Response\Cobranca\ProtestoNegativacaoResposta;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Cobrança Bradesco — ciclo de vida do título: registro, alteração (instrução),
 * baixa e protesto/negativação.
 *
 * FAMÍLIA: open_api (host openapi.bradesco.com.br). Cada operação vive num
 * MICROSERVIÇO PRÓPRIO — por isso quatro base paths distintos abaixo, e não um
 * prefixo único do produto:
 *
 *   - registrar()             POST /boleto/cobranca-registro/v1/cobranca
 *   - alterar()               PUT  /boleto/cobranca-altera/v1/alterar
 *   - baixar()                POST /boleto/cobranca-baixa/v1/baixar
 *   - protestarOuNegativar()  POST /boleto/cobranca-protesto-negativacao/v1/executar
 *
 * Convenções do produto que valem pra todos os métodos:
 *   - Valores monetários trafegam SEM separador, em centavos ("1000" = R$ 10,00).
 *   - O CPF/CNPJ do beneficiário vai SEMPRE decomposto em raiz + filial +
 *     controle (`cpfCnpj`/`nuCPFCNPJ` + `filial` + `controle`).
 *   - `negociacao`/`contaProduto` = agência (4 posições, sem dígito) seguida da
 *     conta (7 posições, sem dígito).
 *   - `nossoNumero` vai SEM o dígito verificador.
 */
final class CobrancaMethods extends BaseMethods
{
    private const PATH_REGISTRO = '/boleto/cobranca-registro/v1/cobranca';

    private const PATH_ALTERACAO = '/boleto/cobranca-altera/v1/alterar';

    private const PATH_BAIXA = '/boleto/cobranca-baixa/v1/baixar';

    private const PATH_PROTESTO = '/boleto/cobranca-protesto-negativacao/v1/executar';

    /**
     * Registra um boleto de cobrança convencional.
     *
     * Obrigatórios na spec: nuCPFCNPJ, filialCPFCNPJ, ctrlCPFCNPJ, idProduto,
     * nuNegociacao, nuCliente, dtEmissaoTitulo, dtVencimentoTitulo (DD.MM.AAAA),
     * vlNominalTitulo, cdEspecieTitulo e o endereço completo do pagador
     * (nomePagador, logradouroPagador, nuLogradouroPagador, cepPagador,
     * complementoCepPagador, bairroPagador, municipioPagador, ufPagador).
     *
     * @param  array<string, mixed>  $dados  Corpo `BoletoRequestDTO` da spec.
     */
    public function registrar(array $dados): BoletoRegistrado
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_REGISTRO, body: $dados);

        return BoletoRegistrado::fromArray($data['data'] ?? $data);
    }

    /**
     * Altera (envia instrução para) um título já registrado: vencimento,
     * abatimento, desconto, protesto, multa, juros, dados do pagador etc.
     *
     * ATENÇÃO: o verbo aqui é PUT (os demais serviços do produto são POST).
     *
     * Corpo esperado: cpfCnpj{cpfCnpj,filial,controle}, produto, negociacao,
     * nossoNumero, dadosPagador{...} e dadosTitulo{...} — só os blocos que
     * você quer alterar precisam vir preenchidos.
     *
     * @param  array<string, mixed>  $dados  Corpo `RequestDTO` da spec.
     */
    public function alterar(array $dados): AlteracaoBoletoResposta
    {
        $data = $this->makeRequest(HttpMethod::PUT, self::PATH_ALTERACAO, body: $dados);

        return AlteracaoBoletoResposta::fromArray($data['data'] ?? $data);
    }

    /**
     * Solicita a baixa de um título.
     *
     * Corpo: cpfCnpj{cpfCnpj,filial,controle}, produto, negociacao,
     * nossoNumero, sequencia (fixo 0) e codigoBaixa (motivo da baixa).
     *
     * @param  array<string, mixed>  $dados  Corpo `RequestDTO` da spec.
     */
    public function baixar(array $dados): BaixaTituloResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_BAIXA, body: $dados);

        return BaixaTituloResposta::fromArray($data['data'] ?? $data);
    }

    /**
     * Protesta, susta protesto, negativa ou cancela negativação de um boleto.
     *
     * `codigoFuncao`: 1 = instrução de protesto, 2 = sustação de protesto,
     * 3 = instrução de negativação, 4 = excluir negativação.
     *
     * `parmFuncao` depende da função:
     *   função 1 → 'P' (protesto comum) ou 'F' (protesto falimentar);
     *   função 2 → 'B' (sustação com baixa) ou 'S' (sem baixa);
     *   função 3 → em branco;
     *   função 4 → 'B' (exclusão com baixa) ou 'S' (sem baixa).
     *
     * @param  array<string, mixed>  $dados  Corpo `RequestDTO` da spec.
     */
    public function protestarOuNegativar(array $dados): ProtestoNegativacaoResposta
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_PROTESTO, body: $dados);

        return ProtestoNegativacaoResposta::fromArray($data['data'] ?? $data);
    }
}
