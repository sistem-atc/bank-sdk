<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Webhooks;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use SistemAtc\Banks\Contracts\PagBrasilIntegration;
use SistemAtc\Banks\Exceptions\PagBrasilSignatureException;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\BoletosPaidNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\ConsentNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\LegacySubscriptionNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\Notification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\PayeeNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\PaymentNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\PayoutNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\ProductNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\ShippingCanceledNotification;
use SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks\SubscriptionNotification;
use SistemAtc\Banks\PagBrasil\Support\Credentials;
use SistemAtc\Banks\PagBrasil\Support\ParsedResponse;
use SistemAtc\Banks\PagBrasil\Support\Signature;

/**
 * Autentica e interpreta IPNs/webhooks da PagBrasil.
 *
 *   $notificacao = Bank::PagBrasil->webhook($integration)->parse($request->all());
 *   return response(WebhookVerifier::acknowledgement());
 *
 * Aceita o payload como array (POST de formulário, JSON já decodificado) ou
 * como o corpo cru (JSON ou XML — o formato do webhook é configurável no
 * Dashboard).
 *
 * Autenticação, nesta ordem:
 *   1. `secret` tem de ser a secret phrase da integração — sempre exigido;
 *   2. havendo signature key cadastrada e `signature` no payload, o HMAC tem
 *      de conferir. A doc aceita "secret e/ou signature"; o SDK exige o
 *      secret e, quando dá, confere os dois.
 * Falhou → PagBrasilSignatureException: NÃO processe.
 *
 * A fonte do HMAC muda por tipo de notificação:
 *   - IPN de pagamento/estorno: só order + amount_brl + payment_status;
 *   - IPN de boletos pagos: o XML de `content` inteiro;
 *   - demais webhooks: todos os valores escalares na ordem, menos secret,
 *     pbtoken e signature (objetos/listas ficam fora).
 *
 * Responda com acknowledgement() ANTES de processar (a doc manda processar
 * depois de confirmar). Resposta fora do padrão = a PagBrasil marca como
 * "Not Compliant" e NÃO reenvia.
 */
final class WebhookVerifier
{
    private const SUBSCRIPTION_EVENTS = [
        'subscription_paused',
        'subscription_reactivated',
        'subscription_canceled',
        'subscription_cycle_skipped',
        'subscription_cycle_unskipped',
        'subscription_frequency_changed',
        'subscription_billing_day_changed',
        'subscription_payment_method_updated',
    ];

    private readonly PagBrasilIntegration $integration;

    public function __construct(PagBrasilIntegration $integration)
    {
        $this->integration = Credentials::of($integration);
    }

    /**
     * Texto que a PagBrasil exige como resposta: "Received successfully [timestamp]".
     */
    public static function acknowledgement(?DateTimeInterface $at = null): string
    {
        $at ??= new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'));

        return 'Received successfully '.$at->format('Y-m-d H:i:s');
    }

    /**
     * Autentica e devolve a notificação tipada.
     *
     * @param  array<string, mixed>|string  $payload
     *
     * @throws PagBrasilSignatureException origem não comprovada
     * @throws InvalidArgumentException payload que não é nenhuma notificação conhecida
     */
    public function parse(array|string $payload): Notification
    {
        $fields = is_string($payload) ? $this->decode($payload) : $payload;

        $this->assertSecret($fields);

        // Boletos pagos: a lista vem como XML dentro do campo `content`, e o
        // HMAC é sobre esse XML BYTE A BYTE — inclusive as quebras de linha
        // CRLF (o exemplo da doc só fecha em 555 bytes com \r\n; com \n dá
        // 535 e outra assinatura). Nunca normalize/trim o content antes.
        if (isset($fields['content']) && is_string($fields['content'])) {
            $this->assertSignature($fields, [[$fields['content']]]);

            return $this->boletos($fields['content']);
        }

        if (isset($fields['payment_status'], $fields['order'])) {
            $this->assertSignature($fields, [[
                (string) $fields['order'],
                (string) ($fields['amount_brl'] ?? ''),
                (string) $fields['payment_status'],
            ]]);

            return PaymentNotification::fromArray($fields);
        }

        $this->assertSignature($fields, $this->genericSources($fields));

        $eventType = isset($fields['event_type']) ? (string) $fields['event_type'] : null;
        $action = isset($fields['action']) ? (string) $fields['action'] : null;

        return match (true) {
            in_array($eventType, self::SUBSCRIPTION_EVENTS, true) => SubscriptionNotification::fromArray($fields),
            $eventType === 'shipping_canceled' => ShippingCanceledNotification::fromArray($this->booleans($fields, ['paid_cycle'])),
            $action === 'consent' => ConsentNotification::fromArray($fields),
            in_array($action, ['addpayee', 'updatepayee', 'deletepayee'], true) => PayeeNotification::fromArray($fields),
            in_array($action, ['addpayout', 'successpayout', 'failpayout'], true) => PayoutNotification::fromArray($fields),
            isset($fields['sku']) => ProductNotification::fromArray($this->booleans($fields, ['order_trigger'])),
            isset($fields['subscription']) && is_scalar($fields['subscription']) => LegacySubscriptionNotification::fromArray($fields),
            default => throw new InvalidArgumentException(
                'PagBrasil: notificação de formato desconhecido (campos: '.implode(', ', array_keys($fields)).').'
            ),
        };
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function assertSecret(array $fields): void
    {
        $received = $fields['secret'] ?? null;

        if (! is_string($received) || ! hash_equals($this->integration->getSecretPhrase(), $received)) {
            throw new PagBrasilSignatureException('PagBrasil: secret da notificação não confere com a da integração.');
        }
    }

    /**
     * @param  array<string, mixed>  $fields
     * @param  list<list<string>>  $sources  fontes candidatas (aceita a que bater)
     */
    private function assertSignature(array $fields, array $sources): void
    {
        $key = Credentials::signatureKey($this->integration);
        $signature = $fields['signature'] ?? null;

        if ($key === null || ! is_string($signature) || $signature === '') {
            return; // sem chave ou sem assinatura: o secret já autenticou
        }

        foreach ($sources as $values) {
            if (Signature::matches($signature, $values, $key)) {
                return;
            }
        }

        throw new PagBrasilSignatureException('PagBrasil: assinatura HMAC da notificação não confere.');
    }

    /**
     * A doc não diz como um booleano (ex.: paid_cycle) entra na fonte do
     * HMAC; aceita as duas representações usuais ("true"/"1").
     *
     * @param  array<string, mixed>  $fields
     * @return list<list<string>>
     */
    private function genericSources(array $fields): array
    {
        $exclude = ['signature', 'secret', 'pbtoken'];
        $asWords = Signature::valuesOf($fields, $exclude);

        $asDigits = Signature::valuesOf(
            array_map(fn ($v) => is_bool($v) ? ($v ? '1' : '0') : $v, $fields),
            $exclude,
        );

        return $asWords === $asDigits ? [$asWords] : [$asWords, $asDigits];
    }

    private function boletos(string $content): BoletosPaidNotification
    {
        $parsed = ParsedResponse::parse($content);

        // A doc manda só confirmar o recebimento se o XML fechou
        // (</boletos_list>): conteúdo truncado não é lista válida.
        if (! $parsed->isXml() || $parsed->rootName !== 'boletos_list') {
            throw new InvalidArgumentException('PagBrasil: `content` do IPN de boletos não é uma <boletos_list> completa.');
        }

        return BoletosPaidNotification::fromArray(['boletos' => array_values($parsed->data)]);
    }

    /**
     * @param  array<string, mixed>  $fields
     * @param  list<string>  $names
     * @return array<string, mixed>
     */
    private function booleans(array $fields, array $names): array
    {
        foreach ($names as $name) {
            if (isset($fields[$name]) && ! is_bool($fields[$name])) {
                $fields[$name] = filter_var($fields[$name], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $fields;
    }

    /** @return array<string, mixed> */
    private function decode(string $body): array
    {
        $parsed = ParsedResponse::parse($body);

        if (! $parsed->isText()) {
            return $parsed->data;
        }

        parse_str(trim($body), $fields);

        return $fields;
    }
}
