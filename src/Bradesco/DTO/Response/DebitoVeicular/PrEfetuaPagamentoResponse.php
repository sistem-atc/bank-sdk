<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * ⚠️ MOVIMENTA DINHEIRO — retorno da consistência/efetivação do pagamento de
 * débito veicular do PR.
 *
 * A spec descreve a operação em DUAS ETAPAS (consistência e depois efetivação),
 * selecionadas pelo `codigoFuncao` da requisição — exemplo da spec: 'C'.
 * Confira `codigoRetorno` (0 = ok), `codigoMensagem` e `codigoAutenticacao`
 * (autenticação bancária do comprovante) antes de dar a operação por
 * concluída: no Bradesco, erro de negócio chega com HTTP 200.
 *
 * Origem: POST /v1/debitos-veiculares-pr/efetua-pagamento/efetuaPagamentoPR
 */
final class PrEfetuaPagamentoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeTributo = null,  // ex.: "COTA UNICA"
        public readonly ?int $codigoRenavam = null,  // ex.: 323030939
        public readonly ?string $codigoUf = null,  // ex.: "PR"
        public readonly ?string $codigoBarra = null,
        public readonly ?float $valorContaTributo = null,  // ex.: 1190.0
        public readonly ?int $digitoConta = null,  // ex.: 9
        public readonly ?int $codigoMunicipioSefaz = null,  // ex.: 0
        public readonly ?int $codigoBanco = null,  // ex.: 237
        public readonly ?int $codigoConta = null,  // ex.: 999
        public readonly ?int $numeroDocumento = null,  // ex.: 0
        public readonly ?string $codigoLocal = null,
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD2782"
        public readonly ?string $dataPagamento = null,
        public readonly ?string $horaPagamento = null,
        public readonly ?int $anoExercicio = null,  // ex.: 2023
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $codigoAutenticacao = null,  // ex.: 0
        public readonly ?int $nsuBanco = null,  // ex.: 28906
        public readonly ?string $codigoPlaca = null,  // ex.: "HOC1377"
        public readonly ?string $descricaoTributo = null,  // ex.: "13/12/2024"
        public readonly ?string $dataVencimento = null,  // ex.: "13/12/2024"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $nomeCliente = null,
        public readonly ?string $devedorPrincipal = null,  // ex.: "SANDRA REGINA SOUZA CARDOSO"
        public readonly ?int $codigoTributo = null,  // ex.: 451
        public readonly ?string $descricaoMensagem = null,  // ex.: "CONSISTENCIA DOS TRIBUTOS REALIZADAS  COM SUCESSO"
        public readonly ?int $codigoAgencia = null,  // ex.: 145
        public readonly ?string $tipoConta = null,  // ex.: "C"
    ) {}
}
