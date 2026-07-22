<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Consulta dos cadastros de webhook de um beneficiário (GET
 * `notificacoes_boletos`) — traz todos os tipos de notificação assinados
 * para aquela conta.
 *
 * @property list<NotificacaoBoleto> $data
 */
final class NotificacaoBoletoList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<NotificacaoBoleto> $data */
    public function __construct(
        #[ArrayOf(NotificacaoBoleto::class)]
        public readonly array $data = [],
        public readonly ?int $page = null,
        public readonly ?int $totalPages = null,
        public readonly ?int $totalElements = null,
    ) {}
}
