<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request;

use BackedEnum;
use DateTimeInterface;
use ReflectionClass;
use ReflectionParameter;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\Common\Traits\StringCase;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Ignore;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;

/**
 * Serializa um DTO de request lendo o CONSTRUTOR (propriedades promovidas):
 *
 *   - chave: #[JsonKey] ou camelCase → snake_case;
 *   - null e lista vazia são omitidos (a PagBrasil trata ausente como "use o default");
 *   - #[Money] → string com 2 casas; enum → value; data → Y-m-d;
 *   - #[Flatten] → os campos do DTO aninhado sobem pro nível de cima;
 *   - DTO aninhado / lista de DTOs → toArray() recursivo;
 *   - #[Ignore] → o DTO traduz o campo sozinho em fixedFields().
 *
 * `fixedFields()` acrescenta campos constantes do tipo de requisição
 * (ex.: payment_method=X, pix_rec=1), que o chamador não precisa informar.
 */
trait SerializesRequest
{
    use StringCase;

    /** @return array<int|string, mixed> */
    public function toArray(): array
    {
        $result = $this->fixedFields();
        $constructor = (new ReflectionClass($this))->getConstructor();

        foreach ($constructor?->getParameters() ?? [] as $param) {
            if ($param->getAttributes(Ignore::class) !== []) {
                continue;
            }

            $value = $this->{$param->getName()};

            if ($value === null || $value === []) {
                continue;
            }

            if ($param->getAttributes(Flatten::class) !== [] && $value instanceof RequestPayload) {
                $result += $value->toArray();

                continue;
            }

            $result[self::keyOf($param)] = self::serializeValue($value, $param->getAttributes(Money::class) !== []);
        }

        return $result;
    }

    /** @return array<string, mixed> */
    protected function fixedFields(): array
    {
        return [];
    }

    private static function keyOf(ReflectionParameter $param): string
    {
        $attrs = $param->getAttributes(JsonKey::class);

        return $attrs === [] ? self::camelToSnake($param->getName()) : $attrs[0]->newInstance()->key;
    }

    private static function serializeValue(mixed $value, bool $money): mixed
    {
        return match (true) {
            $money && (is_int($value) || is_float($value) || is_string($value)) => self::money($value),
            $value instanceof BackedEnum => $value->value,
            $value instanceof DateTimeInterface => $value->format('Y-m-d'),
            $value instanceof RequestPayload => $value->toArray(),
            is_array($value) => array_map(fn ($item) => self::serializeValue($item, $money), $value),
            default => $value,
        };
    }

    /**
     * Normaliza pra "123.45". String já decimal é tratada como TEXTO (sem
     * passar por float, que perderia precisão em valores grandes); vírgula
     * decimal ("1.234,56") é aceita.
     */
    private static function money(int|float|string $value): string
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = str_contains($value, ',') ? str_replace(['.', ','], ['', '.'], $value) : $value;

            if (preg_match('/^(-?\d+)(?:\.(\d{1,2}))?$/', $value, $m)) {
                return $m[1].'.'.str_pad($m[2] ?? '', 2, '0');
            }
        }

        return number_format((float) $value, 2, '.', '');
    }
}
