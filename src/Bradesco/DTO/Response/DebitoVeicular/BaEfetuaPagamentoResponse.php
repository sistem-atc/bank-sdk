<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * ⚠️ MOVIMENTA DINHEIRO — retorno da consistência/efetivação do pagamento de
 * débito veicular da BA.
 *
 * `codigoFuncao` da requisição decide o desfecho ('C' consiste, 'P' paga).
 * `statusPagamento` + `codigoRetorno` + `codigoMensagem` dizem o resultado;
 * `nsuBanco`/`nsuOrigem`/`nsuProdeb` são os identificadores de rastreio.
 *
 * Origem: POST /v1/debitos-veiculares-ba/renavam/efetua-pagamento/efetuaPagamentoBA
 */
final class BaEfetuaPagamentoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoRegraPositividade = null,
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        public readonly ?int $codigoRenavam = null,  // ex.: 214059219
        public readonly ?int $numeroMulta = null,  // ex.: 300261691
        public readonly ?int $localEntrega = null,  // ex.: 0
        public readonly ?int $anoCrvl = null,  // ex.: 0
        public readonly ?string $codigoUf = null,  // ex.: "BA"
        public readonly ?int $statusPagamento = null,  // ex.: 0
        public readonly ?string $sequencialPeriferico = null,  // ex.: "LVBA"
        public readonly ?int $nsuOrigem = null,  // ex.: 26592
        public readonly ?float $valorTotal = null,  // ex.: 136.85
        public readonly ?int $digitoConta = null,  // ex.: 9
        public readonly ?int $codigoConta = null,  // ex.: 999
        public readonly ?string $codigoLocal = null,
        public readonly ?int $numeroParcela = null,  // ex.: 1
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 91
        public readonly ?int $quantidadeMinimaAnalise = null,  // ex.: 0
        /** @var array<int, mixed> */
        public readonly array $lista = [],
        public readonly ?float $valorDespesaOperacional = null,  // ex.: 0
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD2782"
        public readonly ?int $nsuProdeb = null,  // ex.: 29174377
        public readonly ?float $valorTarifaPostagem = null,  // ex.: 0
        public readonly ?string $nomeProprietario = null,
        public readonly ?string $caracteristicaOperLynx = null,
        public readonly ?int $anoExercicio = null,  // ex.: 0
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $agenciaDestino = null,  // ex.: 145
        public readonly ?string $codigoPlaca = null,
        public readonly ?float $valorTarifaBancaria = null,  // ex.: 0
        public readonly ?int $codigoPagamento = null,  // ex.: 403
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $nomeCliente = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 259934535
        public readonly ?int $codigoMunicipio = null,  // ex.: 0
        public readonly ?string $descricaoMensagem = null,  // ex.: "CONSISTENCIA DOS TRIBUTOS REALIZADAS  COM SUCESSO"
        public readonly ?string $nomeMunicipio = null,
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 0
    ) {}
}
