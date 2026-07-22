<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Statement;

use RuntimeException;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\Statement\BalancesResponse;
use SistemAtc\Banks\Itau\DTO\Response\Statement\InterestBearingIncome;
use SistemAtc\Banks\Itau\DTO\Response\Statement\JudicialOrdersResponse;
use SistemAtc\Banks\Itau\DTO\Response\Statement\StatementEvent;
use SistemAtc\Banks\Itau\DTO\Response\Statement\StatementResponse;

/**
 * Extrato de Contas do Itaú — ACCOUNT STATEMENT API (Cash Management),
 * base path `/account-statement/v1`. Consulta lançamentos, saldos, rendimentos
 * de aplicação automática e ordens judiciais de bloqueio pra conciliação
 * bancária. Só LÊ (não movimenta dinheiro).
 *
 * O `statementsId` é a conta no formato Agência(4) + Conta(7) + DAC(1), ex.:
 * 150001234567. Quando não informado explicitamente, é resolvido das
 * configurações da integração (`statement_id`, ou `agencia`+`conta`+`dac`).
 */
final class StatementMethods extends BaseMethods implements StatementEndpoint
{
    private const BASE = '/account-statement/v1';

    /**
     * Lançamentos da conta (default da integração) no período — satisfaz o
     * contrato StatementEndpoint. Achata os `events` de todos os blocos de
     * `data[]` da 1ª página numa lista de {@see StatementEvent}. Datas em `Y-m-d`.
     *
     * @return list<StatementEvent>
     */
    public function periodo(string $de, string $ate): array
    {
        $extrato = $this->extrato($this->resolveStatementId(), $de, $ate);

        $eventos = [];
        foreach ($extrato->data as $bloco) {
            foreach ($bloco->events as $evento) {
                $eventos[] = $evento;
            }
        }

        return $eventos;
    }

    /**
     * Extrato paginado da conta — `GET /statements/{statementsId}`. Retorna o
     * envelope completo (blocos de lançamentos/saldos + paginação).
     *
     * @param  string  $statementId  Agência(4)+Conta(7)+DAC(1), ex.: 150001234567
     * @param  string  $de  Data inicial `Y-m-d` (obrigatória)
     * @param  string|null  $ate  Data final `Y-m-d` (default: data atual no Itaú)
     * @param  string  $type  "current_account" ou "savings_account"
     */
    public function extrato(
        string $statementId,
        string $de,
        ?string $ate = null,
        string $type = 'current_account',
        int $page = 1,
        int $pageSize = 10,
        bool $showPendingEvents = false,
    ): StatementResponse {
        $query = array_filter([
            'type' => $type,
            'start_date' => $de,
            'end_date' => $ate,
            'page' => $page,
            'page_size' => $pageSize,
            'show_pending_events' => $showPendingEvents ? 'true' : null,
        ], static fn ($v) => $v !== null);

        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/statements/'.rawurlencode($statementId),
            query: $query,
        );

        return StatementResponse::fromArray($data);
    }

    /**
     * Rendimentos diários de aplicação automática —
     * `GET /statements/{statementsId}/interest-bearing-accounts`. A resposta é
     * só `data[]` (sem paginação); devolve a lista hidratada.
     *
     * @return list<InterestBearingIncome>
     */
    public function rendimentos(string $statementId, string $de, ?string $ate = null): array
    {
        $query = array_filter([
            'start_date' => $de,
            'end_date' => $ate,
        ], static fn ($v) => $v !== null);

        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/statements/'.rawurlencode($statementId).'/interest-bearing-accounts',
            query: $query,
        );

        return array_map(
            static fn (array $item) => InterestBearingIncome::fromArray($item),
            $data['data'] ?? [],
        );
    }

    /**
     * Ordens judiciais de bloqueio da conta no período —
     * `GET /statements/{statementsId}/judicial-orders`. Paginado.
     */
    public function ordensJudiciais(
        string $statementId,
        string $de,
        ?string $ate = null,
        int $page = 1,
    ): JudicialOrdersResponse {
        $query = array_filter([
            'start_date' => $de,
            'end_date' => $ate,
            'page' => $page,
        ], static fn ($v) => $v !== null);

        $data = $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/statements/'.rawurlencode($statementId).'/judicial-orders',
            query: $query,
        );

        return JudicialOrdersResponse::fromArray($data);
    }

    /**
     * Posição de saldo (rápida) de todas as contas da integração —
     * `GET /balances`. Não recebe parâmetros; a conta é inferida pelo gateway a
     * partir das credenciais/certificado.
     */
    public function saldos(): BalancesResponse
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/balances');

        return BalancesResponse::fromArray($data);
    }

    /**
     * Resolve o `statementsId` das configurações da integração pro método
     * `periodo()` (que só recebe datas). Aceita a chave pronta `statement_id`
     * ou compõe de `agencia`+`conta`+`dac`.
     */
    private function resolveStatementId(): string
    {
        $settings = $this->integration->getBankSettings();

        if (! empty($settings['statement_id'])) {
            return (string) $settings['statement_id'];
        }

        $composto = (string) ($settings['agencia'] ?? '')
            .(string) ($settings['conta'] ?? '')
            .(string) ($settings['dac'] ?? '');

        if ($composto === '') {
            throw new RuntimeException(
                'statement_id (ou agencia+conta+dac) ausente nas configuracoes da integracao Itau.',
            );
        }

        return $composto;
    }
}
