<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `TransacaoDetalhe` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `TransacaoDetalhe`).
 */
final class TransacaoDetalhe implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** ID de identificação do documento. Seu objetivo é possibilitar a conciliação de pagamentos. */
        public readonly ?string $txId = null,
        public readonly ?string $nomePagador = null,
        public readonly ?string $descricaoPix = null,
        public readonly ?string $cpfCnpjPagador = null,
        public readonly ?string $contaBancariaRecebedor = null,
        public readonly ?string $nomeEmpresaPagador = null,
        public readonly ?string $tipoDetalhe = null,
        public readonly ?string $endToEndId = null,
        public readonly ?string $chavePixRecebedor = null,
        public readonly ?string $nomeEmpresaRecebedor = null,
        public readonly ?string $nomeRecebedor = null,
        public readonly ?string $agenciaRecebedor = null,
        public readonly ?string $cpfCnpjRecebedor = null,
        public readonly ?string $origemMovimentacao = null,
        public readonly ?string $codigoSolicitacao = null,
        public readonly ?string $dataVencimento = null,
        public readonly ?string $dataTransacao = null,
        public readonly ?string $nossoNumero = null,
        public readonly ?string $seuNumero = null,
        public readonly ?string $codBarras = null,
        public readonly ?string $juros = null,
        public readonly ?string $multa = null,
        public readonly ?string $desconto1 = null,
        public readonly ?string $desconto2 = null,
        public readonly ?string $desconto3 = null,
        public readonly ?string $nome = null,
        public readonly ?string $dataLimite = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $dataEmissao = null,
        public readonly ?string $abatimento = null,
        public readonly ?string $valorCompra = null,
        public readonly ?string $produto = null,
        public readonly ?string $agencia = null,
        public readonly ?string $numeroChequeBancario = null,
        public readonly ?string $contaBancaria = null,
        public readonly ?string $dataRetorno = null,
        public readonly ?string $motivoRetorno = null,
        public readonly ?string $descricaoChequeBancario = null,
        public readonly ?string $nomeEmpresa = null,
        public readonly ?string $codigoAfiliado = null,
        public readonly ?string $estabelecimento = null,
        public readonly ?string $contaBancariaPagador = null,
        public readonly ?string $descricaoTransferencia = null,
        public readonly ?string $agenciaPagador = null,
        public readonly ?string $bancoRecebedor = null,
        public readonly ?string $idTransferencia = null,
        public readonly ?string $dataEfetivacao = null,
        public readonly ?string $valorTotal = null,
        public readonly ?string $detalheDescricao = null,
        public readonly ?string $adicionado = null,
        public readonly ?string $empresaEmissora = null,
        public readonly ?string $valorOriginal = null,
        public readonly ?string $desconto = null,
        public readonly ?string $valorPrincipal = null,
        public readonly ?string $periodoApuracao = null,
        public readonly ?string $valorAumentado = null,
        public readonly ?string $valorParcial = null,
        public readonly ?string $hora = null,
        public readonly ?string $empresaOrigem = null,
        public readonly ?string $nomeDestinatario = null,
        public readonly ?string $nomeOrigem = null,
        public readonly ?string $codigoReceita = null,
        public readonly ?string $linhaDigitavel = null,
        public readonly ?string $autenticacao = null,
    ) {}
}
