<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Perfil do investidor (suitability) do cliente.
 *
 * Origem: POST /managers-suitability/v1/CustomerProfile/{cpfCnpj}
 */
final class PerfilInvestidorResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Perfil do investidor. */
        public readonly ?string $profile = null,
        /** Fluxo APIC ativado (cliente migrado). */
        public readonly ?bool $isApicEnabled = null,
        /** Codigo identificador do perfil. */
        public readonly ?int $identifierProfileCode = null,
        /** Codigo do perfil do investidor. */
        public readonly ?int $investorProfileCode = null,
        /** Descricao do perfil do investidor. */
        public readonly ?string $investorProfileDescription = null,
        /** Identificador APIC. */
        public readonly ?int $apicId = null,
        /** Data de criacao do perfil. */
        public readonly ?string $profileCreationDate = null,
        /** Versao do questionario respondido. */
        public readonly ?int $answeredQuestionnaireVersion = null,
        /** Pontuacao do questionario. */
        public readonly ?float $score = null,
        /** Canal em que o questionario foi respondido. */
        public readonly ?string $channelQuestionnaireDescription = null,
        /** Codigo do usuario. */
        public readonly ?string $userCode = null,
        /** Assinou termo de recusa. */
        public readonly ?bool $signedRefusalTerm = null,
        /** Data de assinatura do termo. */
        public readonly ?string $termSignDate = null,
        /** CPF do procurador. */
        public readonly ?string $nomineeCpf = null,
        /** Nome do procurador. */
        public readonly ?string $nomineeName = null,
        /** Codigo do perfil do Tesouro Direto. */
        public readonly ?int $treasuryProfileCode = null,
        /** Descricao do perfil do Tesouro Direto. */
        public readonly ?string $treasuryProfileDescription = null,
        /** Carteiras administradas vinculadas. @var array<int, PerfilCarteira> */
        #[ArrayOf(PerfilCarteira::class)]
        public readonly array $portfolios = [],
    ) {}
}
