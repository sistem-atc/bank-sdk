<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Agora;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;

/**
 * Ágora Investimentos (Bradesco) — fachada do produto.
 *
 * O produto é grande e quebrado em microserviços com base paths distintos, um
 * por assunto. Esta fachada só distribui a chamada pro grupo certo, mantendo
 * um único ponto de entrada no connector:
 *
 *   $bradesco->agora()->posicao()->acoes($cpfCnpj, $conta);
 *   $bradesco->agora()->saldos()->global($cpfCnpj, $conta);
 *   $bradesco->agora()->carteira()->resumo($cpfCnpj, $conta);
 *   $bradesco->agora()->extrato()->financeiro($cpfCnpj, $conta, $de, $ate);
 *   $bradesco->agora()->cadastro()->perfilInvestidor($cpfCnpj);
 *
 * Família open_api (host openapi.bradesco.com.br). API SOMENTE LEITURA —
 * consulta de investimentos, nada aqui movimenta dinheiro.
 */
final class AgoraMethods extends BaseMethods
{
    private ?PosicaoMethods $posicao = null;

    private ?SaldoMethods $saldos = null;

    private ?CarteiraMethods $carteira = null;

    private ?ExtratoMethods $extrato = null;

    private ?CadastroMethods $cadastro = null;

    /** Posição consolidada e detalhada (managers-position-mgmt). */
    public function posicao(): PosicaoMethods
    {
        return $this->posicao ??= new PosicaoMethods($this->httpClient, $this->integration);
    }

    /** Saldos e limites (managers-balance-check). */
    public function saldos(): SaldoMethods
    {
        return $this->saldos ??= new SaldoMethods($this->httpClient, $this->integration);
    }

    /** Carteira consolidada por classe de ativo (managers-portfolio-mgmt). */
    public function carteira(): CarteiraMethods
    {
        return $this->carteira ??= new CarteiraMethods($this->httpClient, $this->integration);
    }

    /** Movimentação financeira e margem (managers-statement). */
    public function extrato(): ExtratoMethods
    {
        return $this->extrato ??= new ExtratoMethods($this->httpClient, $this->integration);
    }

    /** Cadastro, liquidação e suitability (microserviços managers-cust-*). */
    public function cadastro(): CadastroMethods
    {
        return $this->cadastro ??= new CadastroMethods($this->httpClient, $this->integration);
    }
}
