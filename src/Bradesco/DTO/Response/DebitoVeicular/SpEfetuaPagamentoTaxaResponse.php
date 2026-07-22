<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * ⚠️ MOVIMENTA DINHEIRO — retorno do pagamento de taxa do DETRAN-SP.
 *
 * `nsuBanco` (enviado na requisição) é o identificador do lançamento e volta
 * na resposta — é o que permite CONSULTAR antes de reenviar em caso de
 * timeout. `codigoRetorno` = 0 com `descricaoMensagem` "PAGAMENTO EFETUADO
 * COM SUCESSO" indica efetivação.
 *
 * Origem: POST /v1/debitos-veiculares-sp/taxas/efetua-pagamento/efetuaPagamentoTaxas
 */
final class SpEfetuaPagamentoTaxaResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $formaPagamento = null,  // ex.: 1
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 1
        public readonly ?int $tipoIdentificacao = null,  // ex.: 1
        public readonly ?int $codigoReceita = null,  // ex.: 4250
        public readonly ?int $localEntrega = null,  // ex.: 1
        public readonly ?int $codigoSubServico = null,  // ex.: 1
        public readonly ?int $quatidadeMsgRodape = null,  // ex.: 1
        public readonly ?int $digitoConta = null,  // ex.: 1
        public readonly ?float $valorTotal = null,  // ex.: 116.5
        public readonly ?int $codigoBanco = null,  // ex.: 237
        public readonly ?int $codigoConta = null,  // ex.: 999
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 55
        #[ArrayOf(SpMensagemRodapeItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD2388"
        public readonly ?int $codigoServico = null,  // ex.: 0
        public readonly ?int $codigoIdentificacao = null,  // ex.: 1
        public readonly ?int $codigoCanal = null,  // ex.: 14
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $nsuBanco = null,  // ex.: 282746
        public readonly ?float $valorTaxaDetran = null,  // ex.: 105.5
        public readonly ?string $codigoPrograma = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 16886413
        public readonly ?string $descricaoMensagem = null,  // ex.: "PAGAMENTO EFETUADO COM SUCESSO"
        public readonly ?string $tipoConta = null,  // ex.: "C"
        public readonly ?int $codigoAgencia = null,  // ex.: 145
    ) {}
}
