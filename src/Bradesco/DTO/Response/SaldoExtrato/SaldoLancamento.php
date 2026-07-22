<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Linha da composição de saldo da conta (produto de saldo: DISPONIVEL,
 * "= TOTAL DE RECURSOS", limites, aplicações…).
 *
 * Origem: GET /v1/fornecimento-saldos-contas/saldos → lstLancamentosSaldos[]
 */
final class SaldoLancamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Descrição completa do produto de saldo. */
        public readonly ?string $nomeProduto = null,
        /** Descrição resumida do produto de saldo. */
        public readonly ?string $nomeProdutoResumido = null,
        /** Código de identificação do produto de saldo (ex.: 999 disponível, 995 total). */
        public readonly ?int $codigoProduto = null,
        /** Identificador de saldo: 0 diversos, 1 c/baixa automática, 2 p/resgate, 3 carência. */
        public readonly ?string $identificadorSaldo = null,
        /** Literal de composição do saldo (A, A+B, A+B+C…), conforme layout de impressão. */
        public readonly ?string $dataLancamentoDb2 = null,
        /** Valor do saldo, string formatada BR ("1.580,12"). */
        public readonly ?string $valorLancamento = null,
        /** Sinal do saldo: '+' positivo, '-' negativo. */
        public readonly ?string $sinalSaldo = null,
    ) {}

    /** Valor do saldo como float, já com o sinal aplicado. */
    public function valor(): ?float
    {
        if ($this->valorLancamento === null || trim($this->valorLancamento) === '') {
            return null;
        }

        $bruto = trim($this->valorLancamento);

        if (str_contains($bruto, ',')) {
            $bruto = str_replace(',', '.', str_replace('.', '', $bruto));
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $bruto) === 1) {
            $bruto = str_replace('.', '', $bruto);
        }

        if (! is_numeric($bruto)) {
            return null;
        }

        $valor = (float) $bruto;

        return $this->sinalSaldo === '-' ? -$valor : $valor;
    }
}
