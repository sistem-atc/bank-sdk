<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `dado_boleto` do Bolecode Pix — o núcleo da cobrança (tipo, carteira,
 * espécie, pagador, os títulos individuais e o QR Code Pix vinculado).
 *
 * No body de SAÍDA a API acrescenta `codigo_tipo_vencimento` (ex.: 3 = data
 * informada pelo cliente). Valores monetários vêm como string.
 *
 * @property list<DadosIndividuaisBoleto> $dadosIndividuaisBoleto
 */
final class DadoBoleto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<DadosIndividuaisBoleto> $dadosIndividuaisBoleto */
    public function __construct(
        public readonly ?string $descricaoInstrumentoCobranca = null,
        public readonly ?string $tipoBoleto = null,
        public readonly ?string $codigoCarteira = null,
        public readonly ?string $codigoEspecie = null,
        public readonly ?string $formaEnvio = null,
        public readonly ?int $codigoTipoVencimento = null,
        public readonly ?string $valorTitulo = null,
        public readonly ?string $valorAbatimento = null,
        public readonly ?string $dataEmissao = null,
        public readonly ?bool $pagamentoParcial = null,
        public readonly ?int $quantidadeMaximoParcial = null,
        public readonly ?Pagador $pagador = null,
        #[ArrayOf(DadosIndividuaisBoleto::class)]
        public readonly array $dadosIndividuaisBoleto = [],
        public readonly ?DadosQrcode $dadosQrcode = null,
    ) {}
}
