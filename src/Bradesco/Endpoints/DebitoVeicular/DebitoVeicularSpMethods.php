<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpComprovanteRenavamResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpComprovanteResumidoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpComprovanteTaxaDetalhadoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpComprovanteTaxaResumidoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpEfetuaPagamentoTaxaResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpListaDebitosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpServicoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpSubServicoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpTipoDebitoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpZeroKmComprovanteDetalhadoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpZeroKmComprovanteResumidoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpZeroKmDebitosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\SpZeroKmPagamentoResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Débito Veicular — SÃO PAULO (DETRAN-SP / SEFAZ-SP) — Bradesco.
 *
 * Permite consultar e PAGAR, debitando a conta corrente do cliente, os débitos
 * de veículos emplacados em SP: IPVA, licenciamento, DPVAT e multas.
 *
 * A API de SP é a mais larga das quatro UFs e se divide em TRÊS conjuntos, cada
 * um com seu próprio prefixo de path:
 *
 *   1. `renavam/`         — débitos de veículo JÁ emplacado, localizados pelo
 *                           RENAVAM (IPVA/licenciamento/multas).
 *   2. `primeiro-veiculo/` — primeiro licenciamento de veículo 0 KM. Aqui o
 *                           veículo ainda NÃO tem RENAVAM, então tudo é
 *                           localizado pelo CPF/CNPJ do adquirente
 *                           (`cpfCnpjPrincipal` + `cpfCnpjFilial` +
 *                           `cpfCnpjDigito`).
 *   3. `taxas/`           — taxas de serviços do DETRAN-SP (CNH, exames,
 *                           vistoria, 2ª via de documento…), localizadas por
 *                           serviço/sub-serviço e pelo `codigoIdentificacao`
 *                           (CPF/CNPJ) do contribuinte.
 *
 * ## Consulta x pagamento
 * CONSULTAM (não movimentam dinheiro):
 *   listarTiposDebitos, listarDebitosRenavam, listarComprovantesRenavam,
 *   consultarComprovanteRenavam, listarDebitosZeroKm,
 *   listarComprovantesZeroKm, consultarComprovanteZeroKm,
 *   listarServicosTaxas, listarSubServicosTaxas, listarComprovantesTaxas,
 *   consultarComprovanteTaxa.
 *
 * ⚠️ DEBITAM A CONTA:
 *   efetuarPagamentoRenavam, efetuarPagamentoZeroKm, efetuarPagamentoTaxas.
 *
 * ## Identificação / idempotência
 * Nenhum endpoint de SP tem chave de idempotência dedicada. Os campos que
 * fazem esse papel na prática, e que você DEVE gerar e persistir do seu lado
 * antes de chamar:
 *   - `nsuBanco`  — NSU do lançamento, enviado por VOCÊ nos três pagamentos e
 *     devolvido na resposta. É o identificador de rastreio da operação.
 *   - `chavePagamento` — devolvida pelas listagens de comprovante; é o que
 *     recupera o comprovante detalhado depois (`AAAAMMDDHHMMSSN`).
 *   - `nsuProdesp` — NSU do lado da PRODESP/DETRAN, só na resposta.
 *   - `identificacaoFuncao` — 'C' nos exemplos da spec (consistência dos
 *     tributos antes da efetivação).
 * Em TIMEOUT: nunca reenvie o pagamento — consulte
 * `listarComprovantes*`/`consultarComprovante*` pelo `nsuBanco`/período antes.
 *
 * ⚠️ Lembre também da regra geral do Bradesco (ver `BaseMethods`): erro de
 * negócio chega com HTTP 200. Só considere pago quando `codigoRetorno` = 0 e a
 * `descricaoMensagem`/`codigoMensagem` confirmarem a efetivação.
 *
 * Família de autorizador: OPEN_API (host openapi.bradesco.com.br) — herdada da
 * base, não sobrescrever.
 *
 * Base path: /v1/debitos-veiculares-sp
 */
final class DebitoVeicularSpMethods extends BaseMethods
{
    private const BASE = '/v1/debitos-veiculares-sp';

    // --- renavam (veículo emplacado) ---
    private const PATH_RENAVAM_TIPOS = self::BASE.'/renavam/lista-tipo-debitos/listaTipoPagamentoTxSP';

    private const PATH_RENAVAM_DEBITOS = self::BASE.'/renavam/lista-debitos/listaDebitosVeicularesSP';

    private const PATH_RENAVAM_PAGAMENTO = self::BASE.'/renavam/efetua-pagamento/efetuaPagamentoSp';

    private const PATH_RENAVAM_COMPROVANTES = self::BASE.'/renavam/lista-comprovantes/listaComprovanteResSp';

    private const PATH_RENAVAM_COMPROVANTE_DET = self::BASE.'/renavam/consulta-comprovante/listaComprovantesDetSP';

    // --- primeiro-veiculo (0 km) ---
    private const PATH_ZEROKM_DEBITOS = self::BASE.'/primeiro-veiculo/lista-debitos/consultarDebitosVeicularesSP';

    private const PATH_ZEROKM_PAGAMENTO = self::BASE.'/primeiro-veiculo/efetua-pagamento/efetuaPagamentoSp';

    private const PATH_ZEROKM_COMPROVANTES = self::BASE.'/primeiro-veiculo/lista-comprovantes/listaComprovanteVeicResSp';

    private const PATH_ZEROKM_COMPROVANTE_DET = self::BASE.'/primeiro-veiculo/consulta-comprovante/listarComprovanteDetalhadoVeiculoZeroKm';

    // --- taxas DETRAN ---
    private const PATH_TAXAS_SERVICOS = self::BASE.'/taxas/lista-servicos/consulta/servico';

    private const PATH_TAXAS_SUBSERVICOS = self::BASE.'/taxas/lista-subservicos/listaTipoSubServicoSP';

    private const PATH_TAXAS_PAGAMENTO = self::BASE.'/taxas/efetua-pagamento/efetuaPagamentoTaxas';

    private const PATH_TAXAS_COMPROVANTES = self::BASE.'/taxas/lista-comprovantes/consulta/comprovante';

    private const PATH_TAXAS_COMPROVANTE_DET = self::BASE.'/taxas/consulta-comprovante/listaComprovanteDetTaxa';

    // =====================================================================
    // RENAVAM — veículo já emplacado
    // =====================================================================

    /**
     * CONSULTA. Tabela de tipos de débito/tributo do DETRAN-SP — devolve os
     * `codigoTributo` aceitos nas demais chamadas (IPVA atual, IPVA anteriores,
     * licenciamento, cota única…).
     *
     * POST /v1/debitos-veiculares-sp/renavam/lista-tipo-debitos/listaTipoPagamentoTxSP
     *
     * @param  array{codigoCanal: int, codigoUf: string}  $dados
     */
    public function listarTiposDebitos(array $dados): SpTipoDebitoResponse
    {
        return SpTipoDebitoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_RENAVAM_TIPOS, body: $dados)
        );
    }

    /**
     * CONSULTA. Débitos veiculares em aberto de um RENAVAM em SP. É a chamada
     * OBRIGATÓRIA antes do pagamento: os itens de `lista` (nomeTributo,
     * anoTributo, codigoTributo, valorTributo, descricaoTributo,
     * indicadorPagamentoTributo) são exatamente o que se repassa em
     * `efetuarPagamentoRenavam()`.
     *
     * POST /v1/debitos-veiculares-sp/renavam/lista-debitos/listaDebitosVeicularesSP
     *
     * @param  array{codigoRenavam: int, digitoConta: int, codigoConta: int, codigoCanal: int, codigoTributo: int, codigoUf: string, codigoAgencia: int, validacaolistaPositiva: string}  $dados
     */
    public function listarDebitosRenavam(array $dados): SpListaDebitosResponse
    {
        return SpListaDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_RENAVAM_DEBITOS, body: $dados)
        );
    }

    /**
     * ⚠️ MOVIMENTA DINHEIRO. Paga os débitos veiculares de um RENAVAM em SP,
     * debitando a conta informada.
     *
     * Campos de identificação/idempotência:
     *   - `nsuBanco` (int): NSU do lançamento — GERADO POR VOCÊ, único por
     *     tentativa. Volta na resposta e é o que amarra o pagamento ao seu
     *     registro. NÃO reutilize num reenvio às cegas.
     *   - `identificacaoFuncao` (string, 1 char): 'C' nos exemplos da spec —
     *     o endpoint faz a consistência dos tributos e a efetivação.
     *   - `identificacaoPeriferico`, `quantidadeOcorrencia`, `dataPagamento`
     *     (`DD.MM.AAAA`) completam o lançamento.
     *   - `lista`: os débitos escolhidos, no mesmo formato devolvido por
     *     `listarDebitosRenavam()` (nomeTributo, anoTributo, descricaoTributo,
     *     indicadorPagamentoTributo, valorTributo, codigoTributo).
     *
     * Em timeout, use `listarComprovantesRenavam()` para checar se o pagamento
     * entrou ANTES de tentar de novo.
     *
     * POST /v1/debitos-veiculares-sp/renavam/efetua-pagamento/efetuaPagamentoSp
     *
     * @param  array{lista: array<int, array<string, mixed>>, dataPagamento: string, identificacaoPeriferico: string, codigoRenavam: int, codigoCanal: int, codigoUf: string, nsuBanco: int, identificacaoFuncao: string, numeroConta: int, digitoConta: int, codigoTributo: int, tipoConta: string, codigoAgencia: int, validacaoListaPositiva: string, quantidadeOcorrencia: int}  $dados
     */
    public function efetuarPagamentoRenavam(array $dados): SpComprovanteRenavamResponse
    {
        return SpComprovanteRenavamResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_RENAVAM_PAGAMENTO, body: $dados)
        );
    }

    /**
     * CONSULTA. Comprovantes (resumidos) de pagamentos já feitos para um
     * RENAVAM num ano. Cada item traz a `chavePagamento`, que é o que
     * `consultarComprovanteRenavam()` exige.
     *
     * POST /v1/debitos-veiculares-sp/renavam/lista-comprovantes/listaComprovanteResSp
     *
     * @param  array{codigoRenavam: int, codigoConta: int, codigoCanal: int, anoPagamento: int, codigoAgencia: int}  $dados
     */
    public function listarComprovantesRenavam(array $dados): SpComprovanteResumidoResponse
    {
        return SpComprovanteResumidoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_RENAVAM_COMPROVANTES, body: $dados)
        );
    }

    /**
     * CONSULTA (2ª via). Comprovante DETALHADO de um pagamento por RENAVAM,
     * recuperado pela `chavePagamento` obtida em
     * `listarComprovantesRenavam()`. Devolve o mesmo envelope do pagamento.
     *
     * POST /v1/debitos-veiculares-sp/renavam/consulta-comprovante/listaComprovantesDetSP
     *
     * @param  array{chavePagamento: int, codigoRenavam: int, codigoCanal: int, codigoTributo: int, anoPagamento: int, codigoUf: string}  $dados
     */
    public function consultarComprovanteRenavam(array $dados): SpComprovanteRenavamResponse
    {
        return SpComprovanteRenavamResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_RENAVAM_COMPROVANTE_DET, body: $dados)
        );
    }

    // =====================================================================
    // PRIMEIRO VEÍCULO (0 km)
    // =====================================================================

    /**
     * CONSULTA. Débitos do primeiro licenciamento de veículo 0 km em SP.
     * Consulta-se pelo CPF/CNPJ do adquirente — o veículo ainda não tem
     * RENAVAM.
     *
     * POST /v1/debitos-veiculares-sp/primeiro-veiculo/lista-debitos/consultarDebitosVeicularesSP
     *
     * @param  array{cpfCnpjFilial: int, codigoConta: int, codigoCanal: int, cpfCnpjPrincipal: int, cpfCnpjDigito: int, codigoUf: string, codigoAgencia: int}  $dados
     */
    public function listarDebitosZeroKm(array $dados): SpZeroKmDebitosResponse
    {
        return SpZeroKmDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_ZEROKM_DEBITOS, body: $dados)
        );
    }

    /**
     * ⚠️ MOVIMENTA DINHEIRO. Paga o primeiro licenciamento do veículo 0 km,
     * debitando a conta informada.
     *
     * Campos de identificação/idempotência:
     *   - `nsuBanco` (int): NSU do lançamento — GERADO POR VOCÊ. Volta na
     *     resposta junto de `nsuProdesp`/`nsuProduto`.
     *   - `codigoFuncao` (string, 1 char): 'C' no exemplo da spec.
     *   - o trio `cpfCnpjPrincipal`/`cpfCnpjFilial`/`cpfCnpjDigito` identifica
     *     o adquirente (não há RENAVAM ainda).
     *   - os valores (`valorTaxaLicenciamento`, `valorTaxaTransferencia`,
     *     `valorTarifaBancaria`) devem vir de `listarDebitosZeroKm()`.
     *
     * Em timeout, confira com `listarComprovantesZeroKm()` antes de reenviar.
     *
     * POST /v1/debitos-veiculares-sp/primeiro-veiculo/efetua-pagamento/efetuaPagamentoSp
     *
     * @param  array{cpfCnpjFilial: int, valorTaxaLicenciamento: float, codigoFuncao: string, codigoCanal: int, codigoUf: string, nsuBanco: int, valorTaxaTransferencia: float, valorTarifaBancaria: float, numeroConta: int, digitoConta: int, cpfCnpjPrincipal: int, cpfCnpjDigito: int, tipoConta: string, codigoAgencia: int}  $dados
     */
    public function efetuarPagamentoZeroKm(array $dados): SpZeroKmPagamentoResponse
    {
        return SpZeroKmPagamentoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_ZEROKM_PAGAMENTO, body: $dados)
        );
    }

    /**
     * CONSULTA. Comprovantes (resumidos) de pagamentos de veículo 0 km, por
     * CPF/CNPJ e período (`dataInicial`/`dataFinal` em `DD.MM.AAAA`). Cada item
     * traz a `chavePagamento` usada na consulta detalhada.
     *
     * POST /v1/debitos-veiculares-sp/primeiro-veiculo/lista-comprovantes/listaComprovanteVeicResSp
     *
     * @param  array{cpfCnpjFilial: int, codigoConta: int, dataInicial: string, cpfCnpjPrincipal: int, cpfCnpjDigito: int, codigoUf: string, codigoAgencia: int, dataFinal: string}  $dados
     */
    public function listarComprovantesZeroKm(array $dados): SpZeroKmComprovanteResumidoResponse
    {
        return SpZeroKmComprovanteResumidoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_ZEROKM_COMPROVANTES, body: $dados)
        );
    }

    /**
     * CONSULTA (2ª via). Comprovante DETALHADO do pagamento de veículo 0 km,
     * recuperado pela `chavePagamento`.
     *
     * POST /v1/debitos-veiculares-sp/primeiro-veiculo/consulta-comprovante/listarComprovanteDetalhadoVeiculoZeroKm
     *
     * @param  array{cpfCnpjFilial: int, chavePagamento: int, codigoConta: int, cpfCnpjPrincipal: int, cpfCnpjDigito: int, codigoUf: string, codigoAgencia: int}  $dados
     */
    public function consultarComprovanteZeroKm(array $dados): SpZeroKmComprovanteDetalhadoResponse
    {
        return SpZeroKmComprovanteDetalhadoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_ZEROKM_COMPROVANTE_DET, body: $dados)
        );
    }

    // =====================================================================
    // TAXAS DETRAN-SP
    // =====================================================================

    /**
     * CONSULTA. Serviços do DETRAN-SP passíveis de cobrança de taxa — devolve
     * os `codigoServico` (CNH, exames, veículos…).
     *
     * POST /v1/debitos-veiculares-sp/taxas/lista-servicos/consulta/servico
     *
     * @param  array{codigoCanal: int}  $dados
     */
    public function listarServicosTaxas(array $dados): SpServicoResponse
    {
        return SpServicoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_TAXAS_SERVICOS, body: $dados)
        );
    }

    /**
     * CONSULTA. Sub-serviços de um `codigoServico`, já com valor da taxa
     * (`valorSubServico`), tarifa de postagem (`valorTarifaPostagem`),
     * `valorTotal` e o `codigoReceita` que o pagamento exige. É a chamada
     * OBRIGATÓRIA antes de `efetuarPagamentoTaxas()`.
     *
     * POST /v1/debitos-veiculares-sp/taxas/lista-subservicos/listaTipoSubServicoSP
     *
     * @param  array{codigoRenavam: int, tipoIdentificacao: int, codigoServico: int, codigoBanco: int, codigoConta: int, codigoCanal: int, codigoAgencia: int}  $dados
     */
    public function listarSubServicosTaxas(array $dados): SpSubServicoResponse
    {
        return SpSubServicoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_TAXAS_SUBSERVICOS, body: $dados)
        );
    }

    /**
     * ⚠️ MOVIMENTA DINHEIRO. Paga uma taxa do DETRAN-SP, debitando a conta
     * informada.
     *
     * Campos de identificação/idempotência:
     *   - `nsuBanco` (int): NSU do lançamento — GERADO POR VOCÊ, volta na
     *     resposta. É o que permite conciliar/consultar depois.
     *   - `identificacaoFuncao` (string, 1 char): 'C' no exemplo da spec.
     *   - `codigoReceita`, `codigoServico`, `codigoSubServico`,
     *     `valorTaxaDetran`, `valorTarifaPostagem` e `valorTotal` devem sair
     *     tal e qual de `listarSubServicosTaxas()`.
     *   - `tipoIdentificacao` + `cpfCnpjPrincipal`/`cpfCnpjFilial`/
     *     `cpfCnpjDigito` identificam o contribuinte.
     *   - `dataDebito` em `DD.MM.AAAA`.
     *
     * Em timeout, confira com `listarComprovantesTaxas()` antes de reenviar.
     *
     * POST /v1/debitos-veiculares-sp/taxas/efetua-pagamento/efetuaPagamentoTaxas
     *
     * @param  array{cpfCnpjFilial: int, valorTarifaPostagem: float, dataDebito: string, tipoIdentificacao: int, codigoRenavam: int, codigoReceita: int, codigoServico: int, codigoCanal: int, codigoSubServico: int, nsuBanco: int, identificacaoFuncao: string, digitoConta: int, valorTaxaDetran: float, valorTotal: float, codigoBanco: int, codigoConta: int, cpfCnpjPrincipal: int, cpfCnpjDigito: int, tipoConta: string, codigoAgencia: int}  $dados
     */
    public function efetuarPagamentoTaxas(array $dados): SpEfetuaPagamentoTaxaResponse
    {
        return SpEfetuaPagamentoTaxaResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_TAXAS_PAGAMENTO, body: $dados)
        );
    }

    /**
     * CONSULTA. Comprovantes (resumidos) de taxas pagas, por
     * `codigoIdentificacao` (CPF/CNPJ) e período (`dataInicio`/`dataFim` em
     * `DD.MM.AAAA`). Cada item traz a `chavePagamento`.
     *
     * POST /v1/debitos-veiculares-sp/taxas/lista-comprovantes/consulta/comprovante
     *
     * @param  array{tipoIdentificacao: int, dataFim: string, codigoBanco: int, codigoConta: int, codigoIdentificacao: int, codigoCanal: int, dataInicio: string, codigoAgencia: int}  $dados
     */
    public function listarComprovantesTaxas(array $dados): SpComprovanteTaxaResumidoResponse
    {
        return SpComprovanteTaxaResumidoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_TAXAS_COMPROVANTES, body: $dados)
        );
    }

    /**
     * CONSULTA (2ª via). Comprovante DETALHADO de uma taxa paga, recuperado
     * pela `chavePagamento`.
     *
     * POST /v1/debitos-veiculares-sp/taxas/consulta-comprovante/listaComprovanteDetTaxa
     *
     * @param  array{chavePagamento: int, codigoIdentificacao: int, codigoConta: int, codigoCanal: int, codigoAgencia: int}  $dados
     */
    public function consultarComprovanteTaxa(array $dados): SpComprovanteTaxaDetalhadoResponse
    {
        return SpComprovanteTaxaDetalhadoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_TAXAS_COMPROVANTE_DET, body: $dados)
        );
    }
}
