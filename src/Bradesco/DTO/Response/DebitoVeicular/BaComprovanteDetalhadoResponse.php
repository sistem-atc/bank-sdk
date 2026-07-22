<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Comprovante DETALHADO (2ª via) de um pagamento de débito veicular da BA.
 *
 * Origem: POST /v1/debitos-veiculares-ba/renavam/lista-comprovante-detalhada/listaComprovanteDetalheBa
 */
final class BaComprovanteDetalhadoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        public readonly ?int $codigoRenavam = null,  // ex.: 214059219
        public readonly ?int $localEntrega = null,  // ex.: 0
        public readonly ?float $valorTotal = null,  // ex.: 136.85
        public readonly ?int $codigoMunicipioSefaz = null,  // ex.: 27400
        public readonly ?string $codigoLocal = null,
        public readonly ?int $numeroParcela = null,  // ex.: 1
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 91
        #[ArrayOf(BaComprovanteTributoItem::class)] public readonly array $lista = [],
        public readonly ?float $valorDespesaOperacional = null,  // ex.: 0
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?int $nsuProdeb = null,  // ex.: 543816435
        public readonly ?string $dataPagamento = null,  // ex.: "29.04.2025"
        public readonly ?float $valorTarifaPostagem = null,  // ex.: 0
        public readonly ?string $horaPagamento = null,  // ex.: "10:11:29"
        public readonly ?int $codigoCanal = null,  // ex.: 66
        public readonly ?string $nomeProprietario = null,  // ex.: "LUIZ CARLOS MAGALHAES SILVA"
        public readonly ?int $numeroContratoPgto = null,  // ex.: 1039219
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $nsuBanco = null,  // ex.: 26592
        public readonly ?string $codigoPlaca = null,  // ex.: "NTK0617"
        public readonly ?int $cpfCnpjCompleto = null,  // ex.: 259934535
        public readonly ?int $codigoMunicipioDetran = null,  // ex.: 3849
        public readonly ?float $valorTarifaBancaria = null,  // ex.: 0
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $nomeCliente = null,  // ex.: "UBIJARA"
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?string $nomeMunicipio = null,  // ex.: "SALVADOR"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
