<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `dado_boleto` — o miolo da cobrança (carteira, espécie, pagador,
 * títulos individuais e as instruções de recebimento). Compartilhado pela
 * emissão (cash_management/v2) e pela consulta de detalhe (boletoscash/v2).
 *
 * Sub-blocos de instrução (juros, multa, desconto, recebimento_divergente,
 * negativação, histórico, mensagens) são mantidos como array cru — variam por
 * versão e não são o alvo principal desta lib.
 *
 * @property list<DadoIndividualBoleto> $dadosIndividuaisBoleto
 */
final class DadoBoleto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<DadoIndividualBoleto> $dadosIndividuaisBoleto */
    public function __construct(
        public readonly ?string $descricaoInstrumentoCobranca = null,
        public readonly ?string $formaEnvio = null,
        public readonly ?string $tipoBoleto = null,
        public readonly ?string $codigoCarteira = null,
        public readonly ?string $codigoEspecie = null,
        public readonly ?string $descricaoEspecie = null,
        public readonly ?string $valorTitulo = null,
        public readonly ?string $dataEmissao = null,
        public readonly ?bool $pagamentoParcial = null,
        public readonly ?int $quantidadeMaximoParcial = null,
        public readonly ?int $codigoTipoVencimento = null,
        public readonly ?string $codigoAceite = null,
        public readonly ?string $indicadorBloqueio = null,
        public readonly ?Pagador $pagador = null,
        public readonly ?SacadorAvalista $sacadorAvalista = null,
        #[ArrayOf(DadoIndividualBoleto::class)]
        public readonly array $dadosIndividuaisBoleto = [],
        /** @var array<string, mixed>|null */
        public readonly ?array $juros = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $multa = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $desconto = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $recebimentoDivergente = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $negativacao = null,
        /** @var array<int, mixed>|null */
        public readonly ?array $listaMensagemCobranca = null,
        /** @var array<int, mixed>|null */
        public readonly ?array $historico = null,
    ) {}
}
