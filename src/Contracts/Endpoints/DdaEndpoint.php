<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts\Endpoints;

/**
 * Consulta de DDA (Débito Direto Autorizado): boletos registrados CONTRA o
 * CNPJ da empresa, prontos pra virar contas a pagar no host.
 *
 * Interface comum a todos os bancos — o consumidor programa contra ela, então
 * Bradesco e Itaú são intercambiáveis (`Bank::Bradesco->dda()` /
 * `Bank::Itau->dda()` devolvem ambos um DdaEndpoint).
 */
interface DdaEndpoint
{
    /**
     * Lista os boletos DDA em aberto no intervalo de vencimento informado.
     *
     * @param  array{vencimento_de?: string, vencimento_ate?: string, situacao?: string}  $filtros
     * @return array<int, \SistemAtc\Banks\Contracts\DTOInterface>
     */
    public function consultar(array $filtros = []): array;
}
