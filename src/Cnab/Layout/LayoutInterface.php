<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Cnab\Layout;

use SistemAtc\Banks\Cnab\CnabType;

/**
 * Descreve o layout posicional de um arquivo CNAB específico (banco + tipo +
 * finalidade — ex.: Bradesco CNAB400 cobrança, Itaú CNAB240 pagamento).
 *
 * É o ponto de extensão do módulo: adicionar suporte a um novo banco/layout é
 * implementar esta interface, sem tocar no parser/builder. Cada implementação
 * carrega o mapa de campos extraído do manual FEBRABAN/banco.
 */
interface LayoutInterface
{
    public function type(): CnabType;

    /**
     * Classifica a linha crua num tipo de registro nomeado (ex.: 'header_arquivo',
     * 'segmento_t', 'detalhe', 'trailer'). O parser usa o retorno pra saber qual
     * fieldMap aplicar.
     */
    public function identifyRecordType(string $line): string;

    /**
     * Mapa de campos do tipo de registro: nome => [posiçãoInicial(1-based), tamanho].
     * Retorna [] pra tipo desconhecido (o parser guarda a linha crua mesmo assim).
     *
     * @return array<string, array{0: int, 1: int}>
     */
    public function fieldMap(string $recordType): array;
}
