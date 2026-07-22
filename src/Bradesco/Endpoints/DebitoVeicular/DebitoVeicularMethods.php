<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;

/**
 * Débito Veicular — Bradesco. FACHADA por UF.
 *
 * O Bradesco não tem uma API única de débito veicular: cada estado é um
 * microserviço próprio, com base path, campos e regras diferentes (a única
 * coisa em comum é a família de autorizador e o fato de TUDO ser POST, até as
 * consultas). Esta fachada só distribui o mesmo client HTTP autenticado para a
 * classe da UF:
 *
 *   ->sp()  → DebitoVeicularSpMethods  (/v1/debitos-veiculares-sp)
 *   ->mg()  → DebitoVeicularMgMethods  (/v1/debitos-veiculares-mg)
 *   ->pr()  → DebitoVeicularPrMethods  (/v1/debitos-veiculares-pr)
 *   ->ba()  → DebitoVeicularBaMethods  (/v1/debitos-veiculares-ba)
 *
 * Uso: `$bradesco->debitoVeicular()->sp()->listarDebitosRenavam([...])`.
 *
 * ⚠️ Em TODAS as UFs existe um `efetuarPagamento*` que DEBITA A CONTA do
 * cliente. Leia o docblock da classe da UF antes de usar: o fluxo de duas
 * etapas (consistência → efetivação), os campos de rastreio (`nsuBanco`,
 * `controleSessao`, `chavePagamento`) e a ausência de chave de idempotência
 * mudam de estado para estado.
 *
 * Família de autorizador: OPEN_API (host openapi.bradesco.com.br) nas quatro.
 */
final class DebitoVeicularMethods extends BaseMethods
{
    private ?DebitoVeicularSpMethods $sp = null;

    private ?DebitoVeicularMgMethods $mg = null;

    private ?DebitoVeicularPrMethods $pr = null;

    private ?DebitoVeicularBaMethods $ba = null;

    /** São Paulo — RENAVAM, primeiro veículo (0 km) e taxas do DETRAN-SP. */
    public function sp(): DebitoVeicularSpMethods
    {
        return $this->sp ??= new DebitoVeicularSpMethods($this->httpClient, $this->integration);
    }

    /** Minas Gerais — inclui a emissão de guia (DAE) com código de barras. */
    public function mg(): DebitoVeicularMgMethods
    {
        return $this->mg ??= new DebitoVeicularMgMethods($this->httpClient, $this->integration);
    }

    /** Paraná. */
    public function pr(): DebitoVeicularPrMethods
    {
        return $this->pr ??= new DebitoVeicularPrMethods($this->httpClient, $this->integration);
    }

    /** Bahia — listagem de débitos por RENAVAM, por ano ou por multa. */
    public function ba(): DebitoVeicularBaMethods
    {
        return $this->ba ??= new DebitoVeicularBaMethods($this->httpClient, $this->integration);
    }
}
