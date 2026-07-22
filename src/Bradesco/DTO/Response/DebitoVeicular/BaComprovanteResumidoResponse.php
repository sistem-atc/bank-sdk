<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista resumida de comprovantes de pagamento de débitos veiculares da BA.
 *
 * Origem: POST /v1/debitos-veiculares-ba/renavam/lista-comprovantes/consulta/resumida
 */
final class BaComprovanteResumidoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cpfCnpjFilial = null,  // ex.: "0"
        #[ArrayOf(BaComprovanteResumidoItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?int $codigoRenavam = null,  // ex.: 214059219
        public readonly ?string $nomeProprietario = null,  // ex.: "LUIZ CARLOS MAGALHAE"
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?string $codigoPlaca = null,  // ex.: "NTK0617"
        public readonly ?string $cpfCnpjCompleto = null,  // ex.: "259934535"
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,  // ex.: "0"
        public readonly ?string $cpfCnpjDigito = null,  // ex.: "91"
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?string $nomeMunicipio = null,  // ex.: "SALVADOR"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
