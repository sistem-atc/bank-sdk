<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\SaqueTroco;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Estabelecimento comercial (ponto de atendimento) do produto Pix Saque e Troco
 * — item de `GET /saque-troco/v1/pontos-atendimento` e resposta de
 * cadastro/atualização (`POST`/`PATCH /pontos-atendimento`).
 *
 * A spec (v1.0.7) não detalha o schema da resposta; os campos abaixo cobrem os
 * atributos citados na descrição (identificador, dados do estabelecimento,
 * horários e situação). Valores monetários da API vêm como string.
 */
final class PontoAtendimento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $pontoAtendimentoId = null,
        public readonly ?string $nome = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $idConta = null,
        public readonly ?string $status = null,
        public readonly ?string $horarioAbertura = null,
        public readonly ?string $horarioFechamento = null,
        public readonly ?string $valorMaximoSaque = null,
        public readonly ?string $valorMaximoTroco = null,
    ) {}
}
