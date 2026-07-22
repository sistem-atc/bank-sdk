<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Comprovante DETALHADO (2ª via) do pagamento de uma taxa do DETRAN-SP,
 * recuperado pela `chavePagamento`.
 *
 * Origem: POST /v1/debitos-veiculares-sp/taxas/consulta-comprovante/listaComprovanteDetTaxa
 */
final class SpComprovanteTaxaDetalhadoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoServico = null,  // ex.: "EXAMES-CNH-CARTEIRA NACIONAL HABILITACAO"
        public readonly ?int $canalPagamento = null,  // ex.: 66
        public readonly ?int $codigoReceita = null,  // ex.: 4250
        public readonly ?int $tipoIdentificacao = null,  // ex.: 1
        public readonly ?int $localEntrega = null,  // ex.: 2
        public readonly ?int $contaCliente = null,  // ex.: 999
        public readonly ?string $autenticacaoDigital = null,
        public readonly ?int $codigoSubServico = null,  // ex.: 6
        public readonly ?float $valorTotal = null,  // ex.: 58.63
        public readonly ?int $nsuOrigem = null,  // ex.: 0
        public readonly ?int $quantidadeMsgRodape = null,  // ex.: 1
        public readonly ?int $numeroDocumento = null,  // ex.: 1024250
        public readonly ?string $codigoLocal = null,
        #[ArrayOf(SpMensagemRodapeItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0001"
        public readonly ?string $descricaoSubServico = null,  // ex.: "Exame Habilitacao Pratico"
        public readonly ?string $dataPagamento = null,  // ex.: "01.02.2024"
        public readonly ?int $codigoServicoDetran = null,  // ex.: 17
        public readonly ?string $dataDebito = null,  // ex.: "01.02.2024"
        public readonly ?int $codigoAgenciaCliente = null,  // ex.: 145
        public readonly ?int $nsuProdesp = null,  // ex.: 100000001
        public readonly ?int $codigoServico = null,  // ex.: 2
        public readonly ?int $codigoIdentificacao = null,  // ex.: 23987015810
        public readonly ?string $horaPagamento = null,  // ex.: "09:46:21"
        public readonly ?int $bancoCliente = null,  // ex.: 237
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?float $valorDespesaPostagem = null,  // ex.: 10.01
        public readonly ?int $nsuBanco = null,  // ex.: 331078
        public readonly ?int $bancoOrigem = null,  // ex.: 0
        public readonly ?float $valorTaxaDetran = null,  // ex.: 48.62
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $digitoContaCliente = null,  // ex.: "7"
        public readonly ?int $quantidadeItens = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "CONSULTA EFETUADA COM SUCESSO"
    ) {}
}
