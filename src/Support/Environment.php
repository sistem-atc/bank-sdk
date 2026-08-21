<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Support;

use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Resolve o ambiente efetivo de uma integração — 'sandbox' ou 'production'.
 *
 * A INTEGRAÇÃO é a autoridade. Cada empresa tem o seu próprio app no banco,
 * com credencial e certificado próprios, e o ambiente é uma propriedade DELA
 * (`isSandbox()`, alimentado pelo cadastro no host). Duas empresas podem estar
 * em estágios diferentes — uma já em produção, outra ainda homologando — e o
 * roteamento tem que acompanhar isso sem depender de variável de ambiente.
 *
 * `banks.sandbox` é só um KILL-SWITCH GLOBAL de homologação: ligado
 * explicitamente (BANKS_SANDBOX=true), força TODAS as integrações pro
 * ambiente de homologação, o que serve pra um staging apontar pro banco de
 * teste sem ter que mexer no cadastro de cada empresa. Desligado — que é o
 * default — ele não opina, e quem manda é a integração.
 *
 * ⚠️ O default deste flag era `true`, e isso invertia a hierarquia: uma
 * integração marcada como produção ia pro sandbox sozinha, em silêncio, a
 * menos que o host declarasse BANKS_SANDBOX=false. Produção não pode depender
 * de uma variável de ambiente estar presente pra funcionar.
 */
final class Environment
{
    public const SANDBOX = 'sandbox';

    public const PRODUCTION = 'production';

    public static function forIntegration(BankIntegration $integration): string
    {
        if ($integration->isSandbox()) {
            return self::SANDBOX;
        }

        return self::globalKillSwitch() ? self::SANDBOX : self::PRODUCTION;
    }

    /** O flag global só vale quando ligado de propósito. */
    private static function globalKillSwitch(): bool
    {
        return (bool) config('banks.sandbox', false);
    }
}
