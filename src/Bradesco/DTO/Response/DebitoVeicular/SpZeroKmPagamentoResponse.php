<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * ⚠️ MOVIMENTA DINHEIRO — retorno do pagamento do primeiro licenciamento de
 * veículo 0 km em SP.
 *
 * `nsuBanco` (enviado na requisição) é o identificador do lançamento e volta
 * na resposta junto de `nsuProdesp`/`nsuProduto` — use-o para conciliar e
 * para CONSULTAR antes de qualquer reenvio.
 *
 * Origem: POST /v1/debitos-veiculares-sp/primeiro-veiculo/efetua-pagamento/efetuaPagamentoSp
 */
final class SpZeroKmPagamentoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        #[ArrayOf(SpMensagemComprovanteItem::class)] public readonly array $listaMsgs = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD2782"
        public readonly ?string $dataPagamento = null,  // ex.: "2025-04-07"
        public readonly ?int $codigoReceita = null,  // ex.: 0
        public readonly ?int $nsuProdesp = null,  // ex.: 100006064
        public readonly ?int $codigoServico = null,  // ex.: 0
        public readonly ?string $horaPagamento = null,
        public readonly ?string $dataContabil = null,
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $razao = null,
        public readonly ?int $nsuBanco = null,  // ex.: 100006064
        public readonly ?string $descricaoTributo = null,  // ex.: "LIC VEICULOS NOVOS/1. REG"
        public readonly ?int $nsuProduto = null,  // ex.: 100006064
        public readonly ?string $codigoPrograma = null,
        public readonly ?int $quantidadeMensagens = null,  // ex.: 1
        public readonly ?int $numeroDocumento = null,  // ex.: 0
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 565715220
        public readonly ?int $codigoTributo = null,  // ex.: 62
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 47
        public readonly ?string $descricaoMensagem = null,  // ex.: "CONSISTENCIA DOS TRIBUTOS REALIZADAS  COM SUCESSO"
    ) {}
}
