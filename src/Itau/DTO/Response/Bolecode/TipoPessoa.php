<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `tipo_pessoa` — usado por beneficiario, pagador e sacador avalista no
 * Bolecode Pix (`POST /recebimentos-pix/v1/boletos_pix`).
 *
 * `codigo_tipo_pessoa` ∈ {F (física), J (jurídica)}. Só um dos documentos vem
 * preenchido conforme o tipo (CPF para F, CNPJ para J).
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
