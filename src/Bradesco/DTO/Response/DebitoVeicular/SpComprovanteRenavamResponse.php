<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Comprovante de débito veicular por RENAVAM em SP.
 *
 * DTO COMPARTILHADO por duas operações — a spec declara o mesmo envelope nas
 * duas:
 *   - POST /renavam/efetua-pagamento/efetuaPagamentoSp (⚠️ MOVIMENTA DINHEIRO)
 *   - POST /renavam/consulta-comprovante/listaComprovantesDetSP (consulta/2ª via)
 *
 * No pagamento, `codigoRetorno`/`codigoMensagem`/`descricaoMensagem` dizem se
 * foi consistência ou efetivação; `nsuBanco` e `nsuProdesp` são os
 * identificadores de rastreio, e `chavePagamento` (devolvida na listagem de
 * comprovantes) é o que permite recuperar o comprovante depois.
 */
final class SpComprovanteRenavamResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,
        public readonly ?string $tipoDigital = null,
        #[ArrayOf(SpDebitoDetalheItem::class)] public readonly array $listaDebito = [],
        public readonly ?int $codigoRenavam = null,
        public readonly ?int $codigoReceita = null,
        public readonly ?int $quantidadeDebitos = null,
        public readonly ?string $codigoUf = null,
        public readonly ?float $valorTaxaTransferencia = null,
        public readonly ?int $protocolo = null,
        public readonly ?string $anoCrlv = null,
        public readonly ?float $valorTotal = null,
        public readonly ?int $quantidadeMensagens = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjDigito = null,
        #[ArrayOf(SpMensagemComprovanteItem::class)] public readonly array $listaMsgs = [],
        public readonly ?string $codigoMensagem = null,
        public readonly ?float $valorTaxaLicenciamento = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?int $codigoServicoDetran = null,
        public readonly ?int $nsuProdesp = null,
        public readonly ?string $horaPagamento = null,
        public readonly ?string $dataArrecadacao = null,
        public readonly ?string $nomeProprietario = null,
        public readonly ?int $codigoRetorno = null,
        public readonly ?float $valorDespesaPostagem = null,
        public readonly ?int $nsuBanco = null,
        public readonly ?string $codigoPlaca = null,
        #[ArrayOf(SpMultaItem::class)] public readonly array $listaMulta = [],
        public readonly ?string $codigoPrograma = null,
        public readonly ?int $quantidadeMultas = null,
        public readonly ?int $codigoTributo = null,
        public readonly ?int $cpfCnpjPrincipal = null,
        public readonly ?string $codigoMunicipio = null,
        public readonly ?string $descricaoMensagem = null,
        public readonly ?string $tipoConta = null,
    ) {}
}
