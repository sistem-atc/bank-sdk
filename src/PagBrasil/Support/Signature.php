<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Support;

/**
 * HMAC-MD5 da PagBrasil — a MESMA regra em todo lugar (respostas XML da API
 * clássica, IPN de pagamento/estorno, lista de boletos pagos, webhooks de
 * consentimento Pix Automático, favorecido e payout):
 *
 *   fonte = concatenação dos valores, na ordem em que aparecem
 *           + o comprimento dessa concatenação
 *   assinatura = hash_hmac('md5', fonte, signature_key)
 *
 * Exemplo da doc (IPN): order=1234567890, amount_brl=39.50, payment_status=P
 *   fonte "123456789039.50P16" + chave 36d5f7…d690 → 3093a7dffa0c04e7…514e
 *
 * ⚠️ ENCODING: a conta é feita sobre os bytes ISO-8859-1 — o XML da PagBrasil
 * declara encoding="ISO-8859-1" e o comprimento que entra na fonte é em bytes
 * Latin-1. Conferido contra os exemplos da doc: o XML do Pix com "José" e
 * "São Paulo" dá comprimento 412 (Latin-1), não 414 (UTF-8), e só a fonte
 * Latin-1 reproduz a assinatura df25f064…c448. Quem calcular sobre a string
 * UTF-8 que o parser de XML devolve erra a assinatura de TODO pedido com
 * acento — e só nesses, o que é pior de achar.
 *
 * Webhooks em JSON (formato configurável no Dashboard) chegam em UTF-8 e a
 * doc não diz em que encoding a PagBrasil assina esse caso; por isso a
 * conferência aceita a fonte Latin-1 e, se não bater, a UTF-8. Para texto só
 * ASCII as duas são idênticas.
 */
final class Signature
{
    /**
     * Assina uma lista de valores (strings UTF-8, como o PHP os tem em mãos).
     *
     * @param  list<string>  $values
     */
    public static function sign(array $values, string $key): string
    {
        return self::hmac(self::toLatin1(implode('', $values)), $key);
    }

    /**
     * A assinatura recebida confere com os valores?
     *
     * @param  list<string>  $values
     */
    public static function matches(?string $signature, array $values, string $key): bool
    {
        if ($signature === null || $signature === '') {
            return false;
        }

        $signature = strtolower(trim($signature));
        $utf8 = implode('', $values);

        if (hash_equals(self::hmac(self::toLatin1($utf8), $key), $signature)) {
            return true;
        }

        return hash_equals(self::hmac($utf8, $key), $signature);
    }

    /**
     * Valores que entram na fonte de uma estrutura achatada (resposta XML,
     * webhook): todo campo escalar, na ordem, menos a própria `signature` e a
     * `secret` (que viaja no webhook mas fica fora da conta). Listas/objetos
     * aninhados ficam de fora — é a regra dos webhooks do PagStream para
     * campos que carregam lista.
     *
     * @param  array<string, mixed>  $fields
     * @param  list<string>  $exclude
     * @return list<string>
     */
    public static function valuesOf(array $fields, array $exclude = ['signature', 'secret']): array
    {
        $values = [];

        foreach ($fields as $name => $value) {
            if (in_array($name, $exclude, true) || is_array($value) || is_object($value) || $value === null) {
                continue;
            }

            $values[] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
        }

        return $values;
    }

    /** HMAC da fonte já montada (bytes) + o seu comprimento em bytes. */
    private static function hmac(string $concatenated, string $key): string
    {
        return hash_hmac('md5', $concatenated.strlen($concatenated), $key);
    }

    private static function toLatin1(string $utf8): string
    {
        // Caractere fora do Latin-1 (emoji, etc.) vira "?", como faria o
        // servidor ao gravar em ISO-8859-1.
        return mb_convert_encoding($utf8, 'ISO-8859-1', 'UTF-8');
    }
}
