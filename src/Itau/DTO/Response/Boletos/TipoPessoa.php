<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `tipo_pessoa` (F/J + CPF ou CNPJ) das APIs de Boletos Cobrança.
 * Usado por beneficiário, pagador e sacador avalista.
 */
final class TipoPessoa implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoTipoPessoa = null,
        public readonly ?string $numeroCadastroPessoaFisica = null,
        public readonly ?string $numeroCadastroNacionalPessoaJuridica = null,
    ) {}
}
