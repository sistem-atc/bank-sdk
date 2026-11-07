<?php

declare(strict_types=1);

namespace SistemAtc\Banks;

use SistemAtc\Banks\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Banks\Inter\Inter;
use SistemAtc\Banks\Banks\Itau\Itau;
use SistemAtc\Banks\Contracts\BankConnector;

/**
 * Identificador dos BANCOS suportados. O enum só nomeia o banco e resolve o
 * connector: toda regra (auth, extrato, Pix, pagamentos, DDA) mora na classe
 * do banco, que pode divergir das outras sem tocar aqui.
 *
 *   Bank::from($conexao->banco)->connector()->statement($integration)->periodo(...);
 *   Itau::make()->boletos($integration)->emissao()->emitir([...]);
 *
 * Gateways de pagamento (PagBrasil) não são banco: ficam no enum Gateway.
 *
 * Pra adicionar um banco: crie src/Banks/{Banco} com o connector implementando
 * BankConnector e acrescente o case + uma linha em connector().
 */
enum Bank: string
{
    case Bradesco = 'bradesco';
    case Inter = 'inter';
    case Itau = 'itau';

    public function connector(): BankConnector
    {
        return match ($this) {
            self::Bradesco => Bradesco::make(),
            self::Inter => Inter::make(),
            self::Itau => Itau::make(),
        };
    }

    /** Código de compensação FEBRABAN (CNAB e conciliação). */
    public function code(): string
    {
        return $this->connector()->code();
    }
}
