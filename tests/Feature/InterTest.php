<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Exceptions\BankRequestException;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\ListaTransacoesCompletaPadrao;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\ListaTransacoesCompletaScroll;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\Saldo;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\TransacaoSimples;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\CobrancaDetalhadaResponseBody;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CobGerada;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\InfoAdicional;
use SistemAtc\Banks\Banks\Inter\Enums\TipoWebhookBanking;
use SistemAtc\Banks\Banks\Inter\Inter;
use SistemAtc\Banks\Banks\Inter\Support\Idempotency;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

const INTER_SANDBOX = 'https://cdpj-sandbox.partners.uatinter.co';
const INTER_PROD = 'https://cdpj.partners.bancointer.com.br';

/** Integração Inter já autenticada (não bate no endpoint de token). */
function inter(array $settings = [], bool $sandbox = true): FakeBankIntegration
{
    Http::preventStrayRequests();

    $i = new FakeBankIntegration(settings: $settings, sandbox: $sandbox);
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 3600;

    return $i;
}

// ─── Enum / connector ────────────────────────────────────────────────────

it('Bank::Inter resolve o connector, o código FEBRABAN 077 e o valor persistido', function () {
    expect(Bank::Inter->connector())->toBeInstanceOf(Inter::class)
        ->and(Bank::Inter->code())->toBe('077')
        ->and(Bank::from('inter'))->toBe(Bank::Inter);
});

it('DDA não existe no Inter e falha de forma explícita', function () {
    Inter::make()->dda(inter());
})->throws(BadMethodCallException::class, 'Inter: não há API de DDA');

// ─── OAuth ───────────────────────────────────────────────────────────────

it('autentica no host do ambiente, com os escopos da integração separados por espaço', function () {
    Http::preventStrayRequests();
    Http::fake([INTER_SANDBOX.'/oauth/v2/token' => Http::response([
        'access_token' => 'NOVO', 'token_type' => 'Bearer', 'expires_in' => 3600, 'scope' => 'extrato.read',
    ])]);

    $i = new FakeBankIntegration(settings: ['scopes' => ['extrato.read', 'cob.write']]);
    $token = Inter::make()->auth($i);

    expect($token->accessToken)->toBe('NOVO')->and($token->expiresIn)->toBe(3600);
    Http::assertSent(fn (Request $r) => $r->url() === INTER_SANDBOX.'/oauth/v2/token'
        && $r['grant_type'] === 'client_credentials'
        && $r['client_id'] === 'cli'
        && $r['client_secret'] === 'sec'
        && $r['scope'] === 'extrato.read cob.write');
});

it('sem escopos na integração, pede os do config', function () {
    Http::preventStrayRequests();
    Http::fake(['*/oauth/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600])]);

    Inter::make()->auth(new FakeBankIntegration());

    Http::assertSent(fn (Request $r) => str_contains($r['scope'], 'extrato.read')
        && str_contains($r['scope'], 'boleto-cobranca.write')
        && str_contains($r['scope'], 'pagamento-pix.write'));
});

it('reaproveita o token válido: só 1 chamada de token pra várias requests', function () {
    Http::preventStrayRequests();
    Http::fake([
        '*/oauth/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/banking/v2/saldo*' => Http::response(['disponivel' => 10.5]),
    ]);

    $i = new FakeBankIntegration();
    Inter::make()->banking($i)->extrato()->saldo();
    Inter::make()->banking($i)->extrato()->saldo();

    Http::assertSentCount(3); // 1 token + 2 saldo
    expect($i->accessToken)->toBe('T');
});

it('host que NÃO guarda o token: o cache do SDK segura o limite de 5 tokens/min', function () {
    Http::preventStrayRequests();
    Http::fake([
        '*/oauth/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/banking/v2/saldo*' => Http::response(['disponivel' => 10.5]),
    ]);

    // Igual ao Bunker: getAccessToken() sempre null, só a validade é gravada.
    $i = new FakeBankIntegration();
    $i->hostKeepsToken = false;

    foreach (range(1, 6) as $_) {
        Inter::make()->banking($i)->extrato()->saldo();
    }

    Http::assertSentCount(7); // 1 token + 6 saldo
});

it('401 descarta o token do cache e reautentica uma vez', function () {
    Http::preventStrayRequests();
    Http::fake([
        '*/oauth/v2/token' => Http::sequence()
            ->push(['access_token' => 'VELHO', 'expires_in' => 3600])
            ->push(['access_token' => 'NOVO', 'expires_in' => 3600]),
        '*/banking/v2/saldo*' => Http::sequence()
            ->push(['title' => 'Unauthorized'], 401)
            ->push(['disponivel' => 1.0]),
    ]);

    $i = new FakeBankIntegration();
    $i->hostKeepsToken = false;

    Inter::make()->banking($i)->extrato()->saldo();

    Http::assertSent(fn (Request $r) => str_contains($r->url(), '/saldo') && $r->hasHeader('Authorization', 'Bearer NOVO'));
});

it('falha de token vira BankAuthenticationException com o detalhe do Inter', function () {
    Http::preventStrayRequests();
    Http::fake(['*/oauth/v2/token' => Http::response(['title' => 'Forbidden', 'detail' => 'Escopo não autorizado'], 403)]);

    Inter::make()->auth(new FakeBankIntegration());
})->throws(BankAuthenticationException::class, 'Falha na autenticação Inter: Escopo não autorizado');

it('integração em produção vai pro host de produção', function () {
    Http::fake(['*' => Http::response(['disponivel' => 1])]);

    Inter::make()->banking(inter(sandbox: false))->extrato()->saldo();

    Http::assertSent(fn (Request $r) => str_starts_with($r->url(), INTER_PROD.'/banking/v2/saldo'));
});

// ─── Header x-conta-corrente ─────────────────────────────────────────────

it('manda x-conta-corrente só com dígitos e sem zeros à esquerda', function () {
    Http::fake(['*' => Http::response(['disponivel' => 1])]);

    Inter::make()->banking(inter(['conta_corrente' => '0012345-6']))->extrato()->saldo();

    Http::assertSent(fn (Request $r) => $r->header('x-conta-corrente') === ['123456']);
});

it('sem conta configurada, não manda x-conta-corrente', function () {
    Http::fake(['*' => Http::response(['disponivel' => 1])]);

    Inter::make()->banking(inter())->extrato()->saldo();

    Http::assertSent(fn (Request $r) => ! $r->hasHeader('x-conta-corrente'));
});

// ─── Banking: extrato e saldo ────────────────────────────────────────────

it('statement()->periodo() devolve as transações do /extrato', function () {
    Http::fake([INTER_SANDBOX.'/banking/v2/extrato*' => Http::response(['transacoes' => [
        ['dataEntrada' => '2026-11-03', 'tipoTransacao' => 'PIX', 'tipoOperacao' => 'C', 'valor' => '150.00', 'titulo' => 'Pix recebido', 'descricao' => 'FULANO'],
    ]])]);

    $lancamentos = Inter::make()->statement(inter())->periodo('2026-11-01', '2026-11-05');

    expect($lancamentos)->toHaveCount(1)
        ->and($lancamentos[0])->toBeInstanceOf(TransacaoSimples::class)
        ->and($lancamentos[0]->valor)->toBe('150.00')
        ->and($lancamentos[0]->tipoOperacao)->toBe('C');
    Http::assertSent(fn (Request $r) => $r['dataInicio'] === '2026-11-01' && $r['dataFim'] === '2026-11-05');
});

it('saldo hidrata os bloqueios e o limite', function () {
    Http::fake(['*/banking/v2/saldo' => Http::response([
        'disponivel' => 2850.55, 'bloqueadoCheque' => 240.25, 'bloqueadoJudicialmente' => 0, 'limite' => 500,
    ])]);

    $saldo = Inter::make()->banking(inter())->extrato()->saldo();

    expect($saldo)->toBeInstanceOf(Saldo::class)
        ->and($saldo->disponivel)->toBe(2850.55)
        ->and($saldo->limite)->toBe(500.0);
});

it('extrato completo distingue paginação padrão e scroll', function () {
    Http::fakeSequence('*/banking/v2/extrato/completo*')
        ->push(['totalPaginas' => 2, 'transacoes' => [['idTransacao' => 'a', 'tipoTransacao' => 'PIX', 'detalhes' => ['endToEndId' => 'E1']]]])
        ->push(['scrollId' => 'XYZ', 'transacoes' => []]);
    Http::preventStrayRequests();

    $i = inter();
    $padrao = Inter::make()->banking($i)->extrato()->extratoCompleto('2026-11-01', '2026-11-05', ['pagina' => 0]);
    $scroll = Inter::make()->banking($i)->extrato()->extratoCompleto('2026-11-01', '2026-11-05', ['scrollEnabled' => true]);

    expect($padrao)->toBeInstanceOf(ListaTransacoesCompletaPadrao::class)
        ->and($padrao->totalPaginas)->toBe(2)
        ->and($padrao->transacoes[0]->idTransacao)->toBe('a')
        ->and($scroll)->toBeInstanceOf(ListaTransacoesCompletaScroll::class);
    Http::assertSent(fn (Request $r) => ($r['scrollEnabled'] ?? null) === 'true');
});

it('exportarPdf devolve o binário decodificado', function () {
    Http::fake(['*/extrato/exportar*' => Http::response(['pdf' => base64_encode('%PDF-1.4 teste')])]);

    expect(Inter::make()->banking(inter())->extrato()->exportarPdf('2026-11-01', '2026-11-05'))->toBe('%PDF-1.4 teste');
});

// ─── Banking: Pix de saída ───────────────────────────────────────────────

it('pix()->pagar() manda chave, valor e x-id-idempotente determinístico', function () {
    Http::fake([INTER_SANDBOX.'/banking/v2/pix' => Http::response([
        'tipoRetorno' => 'APROVACAO', 'codigoSolicitacao' => 'SOL-1', 'dataPagamento' => '2026-11-07', 'dataOperacao' => '2026-11-07',
    ])]);

    $res = Inter::make()->pix(inter())->pagar([
        'chave' => 'financeiro@fornecedor.com.br', 'valor' => '99.90', 'identificador' => 'titulo-42', 'descricao' => 'NF 123',
    ]);

    expect($res->codigoSolicitacao)->toBe('SOL-1');
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->header('x-id-idempotente') === [Idempotency::uuid('titulo-42')]
        && $r['valor'] === 99.9
        && $r['destinatario'] === ['tipo' => 'CHAVE', 'chave' => 'financeiro@fornecedor.com.br']
        && $r['descricao'] === 'NF 123');
});

it('pix()->pagar() repassa o destinatário completo do Inter (copia e cola)', function () {
    Http::fake(['*/banking/v2/pix' => Http::response(['codigoSolicitacao' => 'S'])]);

    Inter::make()->pix(inter())->pagar([
        'valor' => 10, 'identificador' => 'x',
        'destinatario' => ['tipo' => 'PIX_COPIA_E_COLA', 'pixCopiaECola' => '000201...'],
    ]);

    Http::assertSent(fn (Request $r) => $r['destinatario']['tipo'] === 'PIX_COPIA_E_COLA');
});

it('pix()->pagar() sem identificador é recusado antes de chamar o banco', function () {
    Inter::make()->pix(inter())->pagar(['chave' => 'a', 'valor' => 1]);
})->throws(InvalidArgumentException::class, 'identificador');

it('pix()->consultar() usa o codigoSolicitacao e hidrata o histórico', function () {
    Http::fake(['*/banking/v2/pix/SOL-1' => Http::response([
        'transacaoPix' => ['codigoSolicitacao' => 'SOL-1', 'status' => 'PAGO', 'valor' => 99.9],
        'historico' => [['status' => 'AGUARDANDO_APROVACAO'], ['status' => 'PAGO']],
    ])]);

    $res = Inter::make()->pix(inter())->consultar('SOL-1');

    expect($res->transacaoPix->status)->toBe('PAGO')
        ->and($res->historico)->toHaveCount(2);
});

it('a chave de idempotência é UUID v5 estável, e UUID recebido passa direto', function () {
    $a = Idempotency::uuid('titulo-42');

    expect($a)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/')
        ->and(Idempotency::uuid('titulo-42'))->toBe($a)
        ->and(Idempotency::uuid('titulo-43'))->not->toBe($a)
        ->and(Idempotency::uuid('7C9E6679-7425-40DE-944B-E07FC1F90AE7'))->toBe('7c9e6679-7425-40de-944b-e07fc1f90ae7');
});

// ─── Banking: pagamentos ─────────────────────────────────────────────────

it('payments()->pagarBoleto() traduz o contrato comum pro corpo do Inter', function () {
    Http::fake([INTER_SANDBOX.'/banking/v2/pagamento' => Http::response([
        'quantidadeAprovadores' => 1, 'statusPagamento' => 'AGUARDANDO_APROVACAO', 'codigoTransacao' => 'TX-1',
    ])]);

    $res = Inter::make()->payments(inter())->pagarBoleto([
        'linha_digitavel' => '07797000000000000004501008460019310001802680',
        'valor' => '26.80',
        'data_vencimento' => '2026-11-10',
    ]);

    expect($res->codigoTransacao)->toBe('TX-1');
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->data() === [
            'codBarraLinhaDigitavel' => '07797000000000000004501008460019310001802680',
            'valorPagar' => '26.80',
            'dataVencimento' => '2026-11-10',
        ]);
});

it('pagarBoleto() sem data de vencimento é recusado antes de chamar o banco', function () {
    Inter::make()->payments(inter())->pagarBoleto(['linha_digitavel' => '1', 'valor' => '1']);
})->throws(InvalidArgumentException::class, 'dataVencimento');

it('payments()->consultar() filtra por codigoTransacao e falha se não achar', function () {
    Http::fakeSequence('*/banking/v2/pagamento*')
        ->push([['codigoTransacao' => 'TX-1', 'statusPagamento' => 'PAGO']])
        ->push([]);
    Http::preventStrayRequests();

    $i = inter();
    expect(Inter::make()->payments($i)->consultar('TX-1')->statusPagamento)->toBe('PAGO');
    Http::assertSent(fn (Request $r) => $r['codigoTransacao'] === 'TX-1');

    Inter::make()->payments($i)->consultar('NAO-EXISTE');
})->throws(RuntimeException::class, 'não encontrado');

it('webhook do Banking usa o tipo no path', function () {
    Http::fake(['*' => Http::response(null, 204)]);

    Inter::make()->banking(inter())->webhooks()->cadastrar(TipoWebhookBanking::PixPagamento, 'https://erp.exemplo.com/inter');

    Http::assertSent(fn (Request $r) => $r->method() === 'PUT'
        && $r->url() === INTER_SANDBOX.'/banking/v2/webhooks/pix-pagamento'
        && $r['webhookUrl'] === 'https://erp.exemplo.com/inter');
});

// ─── Cobrança v3 (BolePix) ───────────────────────────────────────────────

it('emite cobrança e consulta o detalhe com boleto e Pix', function () {
    Http::fake([
        INTER_SANDBOX.'/cobranca/v3/cobrancas' => Http::response(['codigoSolicitacao' => 'COB-1']),
        INTER_SANDBOX.'/cobranca/v3/cobrancas/COB-1' => Http::response([
            'cobranca' => ['codigoSolicitacao' => 'COB-1', 'seuNumero' => 'NF123', 'valorNominal' => 150.5, 'situacao' => 'A_RECEBER'],
            'boleto' => ['nossoNumero' => '123', 'linhaDigitavel' => '0779...'],
            'pix' => ['txid' => 'TX', 'pixCopiaECola' => '000201...'],
        ]),
    ]);

    $boletos = Inter::make()->cobranca(inter())->boletos();
    $emitida = $boletos->emitir(['seuNumero' => 'NF123', 'valorNominal' => 150.5, 'dataVencimento' => '2026-11-30', 'numDiasAgenda' => 0]);
    $detalhe = $boletos->consultar($emitida->codigoSolicitacao);

    expect($detalhe)->toBeInstanceOf(CobrancaDetalhadaResponseBody::class)
        ->and($detalhe->cobranca->valorNominal)->toBe(150.5)
        ->and($detalhe->boleto->linhaDigitavel)->toBe('0779...')
        ->and($detalhe->pix->pixCopiaECola)->toBe('000201...');
});

it('listar cobranças traduz a paginação pra chave com ponto', function () {
    Http::fake(['*/cobranca/v3/cobrancas*' => Http::response(['totalPaginas' => 1, 'cobrancas' => []])]);

    Inter::make()->cobranca(inter())->boletos()->listar('2026-11-01', '2026-11-30', ['paginaAtual' => 2, 'itensPorPagina' => 50, 'situacao' => 'RECEBIDO']);

    Http::assertSent(function (Request $r) {
        parse_str((string) parse_url($r->url(), PHP_URL_QUERY), $q);

        // PHP troca "." por "_" no parse_str; o que importa é a query crua.
        return str_contains($r->url(), 'paginacao.paginaAtual=2')
            && str_contains($r->url(), 'paginacao.itensPorPagina=50')
            && $q['situacao'] === 'RECEBIDO'
            && $q['dataInicial'] === '2026-11-01';
    });
});

it('cancela cobrança com motivo limitado a 50 caracteres e baixa o PDF', function () {
    Http::fake([
        '*/cobrancas/COB-1/cancelar' => Http::response(null, 202),
        '*/cobrancas/COB-1/pdf' => Http::response(['pdf' => base64_encode('%PDF')]),
    ]);

    $boletos = Inter::make()->cobranca(inter())->boletos();
    $boletos->cancelar('COB-1', str_repeat('x', 80));

    expect($boletos->pdf('COB-1'))->toBe('%PDF');
    Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/cancelar') && mb_strlen($r['motivoCancelamento']) === 50);
});

// ─── Pix recebimento ─────────────────────────────────────────────────────

it('cria COB e hidrata calendário e informações adicionais', function () {
    Http::fake([INTER_SANDBOX.'/pix/v2/cob' => Http::response([
        'txid' => 'abc123', 'status' => 'ATIVA', 'pixCopiaECola' => '000201...',
        'calendario' => ['criacao' => '2026-11-07T10:00:00Z', 'expiracao' => 3600],
        'valor' => ['original' => '37.00'],
        'infoAdicionais' => [['nome' => 'Pedido', 'valor' => '42']],
    ], 201)]);

    $cob = Inter::make()->recebimentosPix(inter())->cob()->criar([
        'calendario' => ['expiracao' => 3600], 'valor' => ['original' => '37.00'], 'chave' => 'chave-pix',
    ]);

    expect($cob)->toBeInstanceOf(CobGerada::class)
        ->and($cob->calendario->expiracao)->toBe(3600)
        ->and($cob->infoAdicionais[0])->toBeInstanceOf(InfoAdicional::class)
        ->and($cob->infoAdicionais[0]->valor)->toBe('42');
});

it('lista Pix recebidos com período RFC 3339 e booleanos como texto', function () {
    Http::fake(['*/pix/v2/pix*' => Http::response(['pix' => [['endToEndId' => 'E1', 'valor' => '10.00']]])]);

    $res = Inter::make()->recebimentosPix(inter())->pixRecebido()
        ->listar('2026-11-01T00:00:00Z', '2026-11-07T23:59:59Z', ['devolucaoPresente' => false, 'paginaAtual' => 0]);

    expect($res->pix[0]->endToEndId)->toBe('E1');
    Http::assertSent(fn (Request $r) => str_contains($r->url(), 'devolucaoPresente=false')
        && str_contains($r->url(), 'paginacao.paginaAtual=0')
        && str_contains($r->url(), 'inicio=2026-11-01T00%3A00%3A00Z'));
});

it('devolução usa PUT com o id da devolução no path', function () {
    Http::fake(['*' => Http::response(['id' => 'D1', 'status' => 'EM_PROCESSAMENTO', 'valor' => '5.00'], 201)]);

    $dev = Inter::make()->recebimentosPix(inter())->pixRecebido()->devolver('E2E1', 'D1', ['valor' => '5.00']);

    expect($dev->status)->toBe('EM_PROCESSAMENTO');
    Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && $r->url() === INTER_SANDBOX.'/pix/v2/pix/E2E1/devolucao/D1');
});

it('utilitários de sandbox recusam integração de produção', function () {
    Inter::make()->recebimentosPix(inter(sandbox: false))->sandbox();
})->throws(LogicException::class, 'não rodam em produção');

// ─── Retry e erros ───────────────────────────────────────────────────────

it('401 reautentica e repete no MESMO produto', function () {
    Http::preventStrayRequests();
    Http::fake([
        '*/oauth/v2/token' => Http::response(['access_token' => 'NOVO', 'expires_in' => 3600]),
        INTER_SANDBOX.'/cobranca/v3/cobrancas/COB-1' => Http::sequence()
            ->push(['title' => 'Unauthorized'], 401)
            ->push(['cobranca' => ['codigoSolicitacao' => 'COB-1']]),
    ]);

    $i = inter();
    $res = Inter::make()->cobranca($i)->boletos()->consultar('COB-1');

    expect($res->cobranca->codigoSolicitacao)->toBe('COB-1')->and($i->accessToken)->toBe('NOVO');
    Http::assertSentCount(3);
});

it('erro RFC 7807 vira mensagem com o detalhe e a 1ª violação', function () {
    Http::fake(['*' => Http::response([
        'title' => 'Requisição inválida', 'detail' => 'Cobrança inválida.',
        'violacoes' => [['razao' => 'deve ser maior que zero', 'propriedade' => 'valor.original']],
    ], 400)]);

    try {
        Inter::make()->recebimentosPix(inter())->cob()->criar([]);
        $this->fail('esperava BankRequestException');
    } catch (BankRequestException $e) {
        expect($e->getMessage())->toBe('[inter] Cobrança inválida. (valor.original: deve ser maior que zero)')
            ->and($e->status())->toBe(400);
    }
});

it('erro no formato antigo do Banking (erro.mensagem) também vira mensagem', function () {
    Http::fake(['*' => Http::response(['erro' => ['mensagem' => 'Requisição inválida', 'mensagemDetalhe' => 'CPF/CNPJ não pode ser nulo']], 400)]);

    Inter::make()->payments(inter())->pagarDarf([]);
})->throws(BankRequestException::class, '[inter] CPF/CNPJ não pode ser nulo');

// ─── DTOs gerados da spec ────────────────────────────────────────────────

it('todos os DTOs do Inter hidratam vazio e fazem ida e volta em camelCase', function () {
    $dir = dirname(__DIR__, 2).'/src/Banks/Inter/DTO/Response';
    $classes = 0;

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
        $relative = substr($file->getPathname(), strlen($dir) + 1, -4);
        $class = 'SistemAtc\\Banks\\Banks\\Inter\\DTO\\Response\\'.str_replace('/', '\\', $relative);

        // toArray() omite nulos e mantém listas vazias (padrão do SDK).
        expect(array_filter($class::fromArray([])->toArray(), static fn ($v) => $v !== []))->toBe([]);
        $classes++;
    }

    expect($classes)->toBeGreaterThan(100);

    // Ida e volta mantém as chaves do Inter (camelCase), não snake_case.
    $cob = CobGerada::fromArray(['txid' => 't', 'pixCopiaECola' => 'x', 'infoAdicionais' => [['nome' => 'a', 'valor' => 'b']]]);
    expect($cob->toArray())->toMatchArray(['txid' => 't', 'pixCopiaECola' => 'x', 'infoAdicionais' => [['nome' => 'a', 'valor' => 'b']]])
        ->not->toHaveKey('pix_copia_e_cola');
});
