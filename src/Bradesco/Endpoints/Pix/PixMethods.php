<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Pix;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Pix\PixPayment;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;

/**
 * PIX Bradesco (pagamento/consulta). MOVIMENTA dinheiro — idempotente por
 * `identificador`. Path/formato a confirmar com a spec real.
 */
final class PixMethods extends BaseMethods implements PixEndpoint
{
    private const PATH = '/pix/v2/pagamentos';

    /** @param array{chave?: string, valor: string, identificador: string, descricao?: string} $dados */
    public function pagar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH, body: $dados);

        return PixPayment::fromArray($data);
    }

    public function consultar(string $identificador): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH.'/'.rawurlencode($identificador));

        return PixPayment::fromArray($data);
    }
}
