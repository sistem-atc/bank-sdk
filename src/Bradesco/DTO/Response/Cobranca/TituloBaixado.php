<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Título baixado da carteira (por pagamento, decurso de prazo ou comando).
 * Origem: POST /boleto/cobranca-baixado-consulta/v1/listar (item de `titulos`)
 */
final class TituloBaixado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dataVencimento = null,
        public readonly ?int $valorTitulo = null,
        public readonly ?int $quantidadeCasaDecimal = null,
        public readonly ?string $nossoNumero = null,
        public readonly ?string $seuNumero = null,
        /** CPF / CNPJ do beneficiário */
        public readonly ?CpfCnpj $cpfCnpjSacado = null,
        public readonly ?string $nomeSacado = null,
        public readonly ?string $dataRegistro = null,
        public readonly ?string $dataEmissao = null,
        public readonly ?int $bancoDepositario = null,
        public readonly ?int $agenciaDepositaria = null,
        public readonly ?int $statusTitulo = null,
        public readonly ?string $descricaoStatusTitulo = null,
        public readonly ?int $especieDocumento = null,
        public readonly ?string $controleParticipante = null,
        /** CPF / CNPJ do beneficiário */
        public readonly ?CpfCnpj $cpfCnpjSacadorAvalista = null,
        public readonly ?string $nomeSacadorAvalista = null,
        public readonly ?string $aceite = null,
        public readonly ?string $rateio = null,
        public readonly ?string $debitoAutomatico = null,
        public readonly ?string $boletoDDA = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $dataBaixa = null,
        public readonly ?int $valorPago = null,
        public readonly ?int $bancoProcedente = null,
        public readonly ?int $agenciaProcedente = null,
    ) {}
}
