<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use DateTimeImmutable;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lançamento de extrato de conta PJ — unidade de conciliação bancária.
 *
 * Origem: GET /v1/fornecimento-extratos-contas/extratos (blocos
 * `extratoUltimosLancamentos`, `extratoLancamentosFuturos` e
 * `extratoPorPeriodo`). O mainframe do Bradesco devolve o MESMO registro em
 * três formatos ligeiramente diferentes; este DTO é a UNIÃO dos campos —
 * o que não vier no bloco fica null.
 *
 * Cuidados do legado (ambos tratados aqui):
 *  - o campo de sinal vem ora como `sinalLancamento`, ora como `sinalLacamento`
 *    (typo do próprio contrato do banco);
 *  - valores chegam ora como string formatada BR ("1.580,12"), ora numéricos —
 *    ficam `?string` e são convertidos pelos helpers `valor()`/`saldo()`.
 */
final class Lancamento implements DTOInterface
{
    use AutoHydrate {
        fromArray as private autoFromArray;
    }
    use CastToArray;

    public function __construct(
        /** Data do lançamento, formato DD/MM/AAAA. */
        public readonly ?string $dataLancamento = null,
        /** Número do documento gerado pelo produto — NÃO é identificador único. */
        public readonly ?string $numeroDocumento = null,
        /** Valor do lançamento (string formatada BR ou numérico). */
        public readonly ?string $valorLancamento = null,
        /** Sinal do lançamento: '+' crédito, '-' débito. */
        public readonly ?string $sinalLancamento = null,
        /** Texto livre formatado por cada produto que efetua lançamentos. */
        public readonly ?string $segundaLinhalLancamento = null,
        /** Saldo da conta depois deste lançamento. */
        public readonly ?string $valorSaldoAposLancamento = null,
        /** Sinal do saldo: '+' positivo, '-' negativo. */
        public readonly ?string $sinalSaldo = null,
        /** Identificação de subcódigo: 'S' sim, 'N' não. */
        public readonly ?string $identificacaoSubCodigo = null,
        /** Tipo do lançamento (ex.: '01' saldo anterior). */
        public readonly ?string $tipoLancamento = null,
        /** Código identificador do lançamento (histórico do banco). */
        public readonly ?string $codigoLancamento = null,
        /** Histórico abreviado (ex.: 'TRANSFE PIX'). */
        public readonly ?string $descritivoLancamentoAbreviado = null,
        /** Histórico completo (ex.: 'TRANSFERENCIA PIX'). */
        public readonly ?string $descritivoLancamentoCompleto = null,
        /** Data do débito de CPMF (legado). */
        public readonly ?string $dataDebitoCpmf = null,
        /** Valor de CPMF (legado). */
        public readonly ?string $valorCpmf = null,
        /** Descrição completa do produto de saldo (blocos de saldo). */
        public readonly ?string $nomeProduto = null,
        /** Descrição resumida do produto de saldo. */
        public readonly ?string $nomeProdutoResumido = null,
        /** Código de identificação do produto de saldo. */
        public readonly ?int $codigoProduto = null,
        /** Identificador de saldo: 0 diversos, 1 c/baixa automática, 2 p/resgate, 3 carência. */
        public readonly ?string $identificadorSaldo = null,
        /** Literal de composição do saldo (A, A+B, A+B+C…). */
        public readonly ?string $dataLancamentoDb2 = null,
    ) {}

    /**
     * Normaliza o typo `sinalLacamento` do contrato antes de hidratar.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        if (! isset($data['sinalLancamento']) && isset($data['sinalLacamento'])) {
            $data['sinalLancamento'] = $data['sinalLacamento'];
        }

        return self::autoFromArray($data);
    }

    /** Lançamento é crédito? (sinal '+') */
    public function ehCredito(): bool
    {
        return $this->sinalLancamento === '+';
    }

    /** Lançamento é débito? (sinal '-') */
    public function ehDebito(): bool
    {
        return $this->sinalLancamento === '-';
    }

    /** Valor do lançamento como float, já com o sinal aplicado (débito negativo). */
    public function valor(): ?float
    {
        $valor = self::paraFloat($this->valorLancamento);

        if ($valor === null) {
            return null;
        }

        return $this->ehDebito() ? -$valor : $valor;
    }

    /** Saldo após o lançamento como float, já com o sinal aplicado. */
    public function saldo(): ?float
    {
        $saldo = self::paraFloat($this->valorSaldoAposLancamento);

        if ($saldo === null) {
            return null;
        }

        return $this->sinalSaldo === '-' ? -$saldo : $saldo;
    }

    /** Histórico do lançamento (completo, caindo pro abreviado). */
    public function historico(): ?string
    {
        return $this->descritivoLancamentoCompleto ?? $this->descritivoLancamentoAbreviado;
    }

    /** Data do lançamento como objeto (aceita DD/MM/AAAA e DDMMAAAA). */
    public function data(): ?DateTimeImmutable
    {
        $data = $this->dataLancamento;

        if ($data === null || $data === '') {
            return null;
        }

        $digitos = preg_replace('/\D/', '', $data) ?? '';

        if (strlen($digitos) !== 8) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!dmY', $digitos);

        return $date === false ? null : $date;
    }

    /** Converte "1.580,12" / "80" / 8000 pra float. Vazio ou não-numérico vira null. */
    private static function paraFloat(?string $valor): ?float
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        $bruto = trim($valor);

        if (str_contains($bruto, ',')) {
            // Formato BR: ponto é separador de milhar, vírgula é decimal.
            $bruto = str_replace(',', '.', str_replace('.', '', $bruto));
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $bruto) === 1) {
            // Só milhar, sem decimal ("1.580").
            $bruto = str_replace('.', '', $bruto);
        }

        return is_numeric($bruto) ? (float) $bruto : null;
    }
}
