<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada em COE (Certificado de Operacoes Estruturadas).
 *
 * Origem: components.schemas.CoeCustodyApiData.
 */
final class PosicaoCoeItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identificador do COE. */
        public readonly ?int $coeId = null,
        /** Nome do COE. */
        public readonly ?string $coeName = null,
        /** Nome do emissor. */
        public readonly ?string $issuerName = null,
        /** Codigo de rating. */
        public readonly ?string $ratingCode = null,
        /** Descricao do produto. */
        public readonly ?string $description = null,
        /** Data de vencimento. */
        public readonly ?string $maturityDate = null,
        /** Valor da operacao. */
        public readonly ?float $operationValue = null,
        /** Valor bruto. */
        public readonly ?float $grossValue = null,
        /** Valor liquido. */
        public readonly ?float $liqValue = null,
        /** Data de carencia. */
        public readonly ?string $lackTime = null,
        /** Status do COE. */
        public readonly ?string $status = null,
        /** Caminho do DIE (Documento de Informacoes Essenciais). */
        public readonly ?string $pathDie = null,
        /** Caminho da nota de corretagem. */
        public readonly ?string $pathBrokerageNote = null,
        /** Codigo da estrategia. */
        public readonly ?string $cdStrategy = null,
        /** Variacao percentual. */
        public readonly ?float $pcVariation = null,
        /** Data da operacao (AAAAMMDD numerico). */
        public readonly ?int $dtOperation = null,
        /** Descricao do ativo-base. */
        public readonly ?string $dsBaseAsset = null,
        /** Data da aplicacao (AAAAMMDD numerico). */
        public readonly ?int $dtAplic = null,
        /** Valorizacao percentual. */
        public readonly ?float $percentAppreciation = null,
        /** Valorizacao em valor. */
        public readonly ?float $valueAppreciation = null,
        /** Valor atualizado. */
        public readonly ?float $valueUpdated = null,
        /** Modalidade. */
        public readonly ?string $modality = null,
        /** Descricao do status. */
        public readonly ?string $dsStatus = null,
        /** Nome do indexador. */
        public readonly ?string $nmIndex = null,
        /** Identificador da operacao. */
        public readonly ?int $operationId = null,
        /** Codigo CETIP/SELIC. */
        public readonly ?string $cetipSelicCode = null,
    ) {}
}
