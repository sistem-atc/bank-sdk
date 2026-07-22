<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Débitos veiculares pendentes na SEFAZ-MG de um RENAVAM.
 *
 * `controleSessao` devolvido aqui é o identificador da SESSÃO de consulta e
 * precisa ser repassado no pagamento e na obtenção de guia.
 *
 * Origem: POST /v1/debitos-veiculares-mg/lista-debitos/listaDebitosPendentesMG
 */
final class MgListaDebitosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $localidadeVeiculo = null,  // ex.: "BELO HORIZONTE - MG"
        public readonly ?string $nomeOrgao = null,  // ex.: "Secretaria de Estado de Fazenda de Minas Gerais"
        public readonly ?string $codigoMensagem = null,  // ex.: "LCBR0000"
        public readonly ?string $descricaoCpfCnpj = null,  // ex.: "CPF"
        public readonly ?int $codigoRenavam = null,  // ex.: 246304715
        public readonly ?int $quantidadeDebitos = null,  // ex.: 1
        public readonly ?string $nomeProprietario = null,  // ex.: "CARLOS ALBERTO SILVA DOS SANTOS"
        public readonly ?string $cpfCnpjCifrado = null,  // ex.: "***.570.816-**"
        public readonly ?string $tipoOperacao = null,  // ex.: "Débito Veicular do Estado de Minas Gerais"
        public readonly ?string $codigoUf = null,  // ex.: "MG"
        public readonly ?string $controleSessao = null,  // ex.: "039290000000799906002463047155362025-05-21-15.54.42.563563"
        public readonly ?string $codigoPlaca = null,  // ex.: "GLD1623"
        #[ArrayOf(MgDebitoItem::class)] public readonly array $debitosListagem = [],
        public readonly ?string $codigoMunicipio = null,  // ex.: "BELO HORIZONTE"
        public readonly ?string $descricaoMensagem = null,  // ex.: "Operação executada com sucesso."
    ) {}
}
