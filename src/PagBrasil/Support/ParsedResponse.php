<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Corpo de resposta/notificação da PagBrasil já interpretado.
 *
 * A mesma API responde em três formatos, e o formato é parte do contrato:
 *
 *   - XML (ISO-8859-1): pedido (`/api/order/add|get|cancel`), link de
 *     pagamento, consulta de recorrência Pix, lista de boletos pagos.
 *   - JSON: 1-Click Pix, token OAuth, toda a API v2 do PagStream — e Payout /
 *     Pix Automático quando o Dashboard está configurado para JSON.
 *   - TEXTO puro: confirmações ("Refund request received", "Credit card
 *     information successfully deleted", "Charges processed.") e TODOS os
 *     erros da API clássica ("Duplicated order.", "Order not found."…), que
 *     vêm com HTTP 200.
 *
 * Por isso nada aqui decide sucesso/erro: quem chama sabe o formato que
 * espera e trata o resto como erro.
 */
final class ParsedResponse
{
    public const XML = 'xml';

    public const JSON = 'json';

    public const TEXT = 'text';

    /** Nomes de elemento XML que a PagBrasil usa como item de lista. */
    private const LIST_ELEMENTS = ['item', 'boleto'];

    /**
     * @param  array<string, mixed>  $data  filhos do elemento-raiz (XML) ou o objeto JSON
     */
    private function __construct(
        public readonly string $format,
        public readonly array $data,
        public readonly string $raw,
        public readonly ?string $rootName = null,
    ) {}

    public static function parse(string $body): self
    {
        $trimmed = trim($body);

        if ($trimmed !== '' && ($trimmed[0] === '{' || $trimmed[0] === '[')) {
            $decoded = json_decode($trimmed, true);

            if (is_array($decoded)) {
                return new self(self::JSON, $decoded, $body);
            }
        }

        if ($trimmed !== '' && $trimmed[0] === '<') {
            $parsed = self::parseXml($trimmed);

            if ($parsed !== null) {
                return new self(self::XML, $parsed[1], $body, $parsed[0]);
            }
        }

        return new self(self::TEXT, [], $body);
    }

    public function isXml(): bool
    {
        return $this->format === self::XML;
    }

    public function isJson(): bool
    {
        return $this->format === self::JSON;
    }

    public function isText(): bool
    {
        return $this->format === self::TEXT;
    }

    /** Estrutura (XML/JSON) sem nenhum campo — ex.: `<request></request>` de pedido inexistente. */
    public function isEmpty(): bool
    {
        return ! $this->isText() && $this->data === [];
    }

    /** Corpo como texto, sem espaços nas pontas (útil pra mensagens/erros). */
    public function text(): string
    {
        return trim($this->raw);
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}|null
     */
    private static function parseXml(string $xml): ?array
    {
        // Sem declaração de encoding e com bytes que não são UTF-8 válido, o
        // corpo só pode ser Latin-1 (o encoding da PagBrasil): converte antes,
        // senão o libxml aborta no primeiro acento.
        if (! str_starts_with($xml, '<?xml') && ! mb_check_encoding($xml, 'UTF-8')) {
            $xml = mb_convert_encoding($xml, 'UTF-8', 'ISO-8859-1');
        }

        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);

        try {
            $loaded = $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOCDATA);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if (! $loaded || $dom->documentElement === null) {
            return null;
        }

        $root = $dom->documentElement;
        $value = self::nodeToValue($root);

        return [$root->nodeName, is_array($value) ? $value : []];
    }

    /**
     * Elemento com filhos vira array associativo; filhos com o mesmo nome
     * repetido (ex.: vários <boleto> em <boletos_list>) viram lista. Folha
     * vira string (o DOM já entrega UTF-8).
     *
     * @return array<string, mixed>|string
     */
    private static function nodeToValue(DOMNode $node): array|string
    {
        $children = [];

        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $children[] = $child;
            }
        }

        if ($children === []) {
            // Elemento-raiz vazio (<request></request>) é "sem campos", não "".
            return $node->parentNode instanceof DOMDocument ? [] : $node->textContent;
        }

        $names = array_map(fn (DOMElement $c) => $c->nodeName, $children);
        $repeated = count($names) !== count(array_unique($names));

        // <item>/<boleto> são sempre elementos de lista — com um só filho, a
        // regra do "nome repetido" viraria objeto e a forma mudaria conforme a
        // quantidade (ex.: <recurrences><item>…</item></recurrences>).
        $listElement = count(array_unique($names)) === 1 && in_array($names[0], self::LIST_ELEMENTS, true);

        if ($repeated || $listElement) {
            return array_map(fn (DOMElement $c) => self::nodeToValue($c), $children);
        }

        $result = [];
        foreach ($children as $child) {
            $result[$child->nodeName] = self::nodeToValue($child);
        }

        return $result;
    }
}
