<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lista resumida dos comprovantes de pagamento de taxas do DETRAN-SP, por
 * CPF/CNPJ e período.
 *
 * Origem: POST /v1/debitos-veiculares-sp/taxas/lista-comprovantes/consulta/comprovante
 */
final class SpComprovanteTaxaResumidoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cpfCnpjFilial = null,  // ex.: 0
        #[ArrayOf(SpComprovanteTaxaItem::class)] public readonly array $lista = [],
        public readonly ?string $codigoMensagem = null,  // ex.: "ARCD0009"
        public readonly ?int $codigoRenavam = null,  // ex.: 0
        public readonly ?string $codigoPrograma = null,
        public readonly ?string $codigoLocal = null,
        public readonly ?int $cpfCnpjPrincipal = null,  // ex.: 201851238
        public readonly ?int $codigoRetorno = null,  // ex.: 0
        public readonly ?int $cpfCnpjDigito = null,  // ex.: 21
        public readonly ?string $descricaoMensagem = null,  // ex.: "NAO EXISTEM MAIS DADOS PARA CONSULTA"
        public readonly ?int $quantidadeOcorrencia = null,  // ex.: 1
    ) {}
}
