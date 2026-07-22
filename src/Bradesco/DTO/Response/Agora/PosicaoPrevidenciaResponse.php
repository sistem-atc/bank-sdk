<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Posicao consolidada de previdencia do cliente.
 *
 * Diferente das demais posicoes, o envelope aqui e proprio (sem `meta`):
 * traz dados cadastrais + as propostas implantadas.
 *
 * Origem: GET /managers-position-mgmt/v1/consolidatedposition/pension/{cpfCnpj}
 */
final class PosicaoPrevidenciaResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Metadados da consulta. */
        public readonly ?Meta $meta = null,
        /** Status code da resposta. */
        public readonly ?int $statusCode = null,
        /** Erros retornados pelo backend. @var array<int, ErroApi> */
        #[ArrayOf(ErroApi::class)]
        public readonly array $errors = [],
        /** Bloco de status da resposta. */
        public readonly ?RespostaBase $response = null,
        /** Codigo de retorno (string neste endpoint). */
        public readonly ?string $code = null,
        /** Local do processamento. */
        public readonly ?int $local = null,
        /** Descricao do retorno. */
        public readonly ?string $description = null,
        /** Local do erro. */
        public readonly ?int $localError = null,
        /** Codigo SQL do backend. */
        public readonly ?int $sqlCode = null,
        /** Codigo de retorno numerico. */
        public readonly ?int $returnCode = null,
        /** Erros de validacao de campo. @var array<int, ErroValidacao> */
        #[ArrayOf(ErroValidacao::class)]
        public readonly array $validationErrors = [],
        /** Mensagem tecnica pro desenvolvedor. */
        public readonly ?string $developerMessage = null,
        /** Inconsistencias de negocio. @var array<int, Inconsistencia> */
        #[ArrayOf(Inconsistencia::class)]
        public readonly array $inconsistences = [],
        /** Conta. */
        public readonly ?int $account = null,
        /** Agencia. */
        public readonly ?int $agency = null,
        /** Data de nascimento. */
        public readonly ?string $birthDate = null,
        /** Codigo da corretora. */
        public readonly ?int $broker = null,
        /** Canal. */
        public readonly ?int $channel = null,
        /** CPF do cliente. */
        public readonly ?string $cpf = null,
        /** Mensagem de retorno (grafia do contrato). */
        public readonly ?string $msgRetorno = null,
        /** Identificacao do funcionario. */
        public readonly ?string $officialIdentification = null,
        /** Propostas implantadas. @var array<int, PrevidenciaProposta> */
        #[ArrayOf(PrevidenciaProposta::class)]
        public readonly array $proposals = [],
        /** Quantidade de propostas. */
        public readonly ?int $proposedQuantity = null,
        /** Identificador do representante. */
        public readonly ?string $representativeId = null,
        /** Sexo. */
        public readonly ?string $gender = null,
        /** Origem. */
        public readonly ?int $source = null,
        /** Usuario. */
        public readonly ?int $user = null,
    ) {}
}
