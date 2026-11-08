<?php

declare(strict_types=1);

namespace SistemAtc\Banks;

use SistemAtc\Banks\Contracts\GatewayConnector;
use SistemAtc\Banks\Gateways\Asaas\Asaas;
use SistemAtc\Banks\Gateways\PagBrasil\PagBrasil;

/**
 * Identificador dos GATEWAYS de pagamento suportados. Gateway não é banco
 * compensado: não tem código FEBRABAN, extrato, DDA nem pagamento SISPAG, então
 * não implementa BankConnector. Cada gateway expõe os próprios produtos.
 *
 *   Gateway::from($conexao->gateway)->connector();
 *   PagBrasil::make()->pedidos($integration)->pix([...]);
 *
 * Pra adicionar um gateway: crie src/Gateways/{Gateway} com o connector
 * implementando GatewayConnector e acrescente o case + uma linha em connector().
 */
enum Gateway: string
{
    case Asaas = 'asaas';
    case PagBrasil = 'pagbrasil';

    public function connector(): GatewayConnector
    {
        return match ($this) {
            self::Asaas => Asaas::make(),
            self::PagBrasil => PagBrasil::make(),
        };
    }
}
