<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

/**
 * Banco que se autentica no webhook por certificado de cliente (mTLS): quem
 * confere é o servidor web que termina o TLS, contra a CA do banco, e repassa
 * o resultado num cabeçalho. O comando `banks:webhooks-nginx` usa isto pra
 * montar a configuração do nginx.
 */
interface RequiresClientCertificate
{
    /** Caminho do bundle PEM com a CA do banco (vem dentro do pacote). */
    public function clientCaPath(): string;

    /** Cabeçalho em que o proxy repassa `$ssl_client_verify`; vazio = conferência desligada. */
    public function clientVerifyHeader(): string;

    /** Nome sugerido do arquivo da CA no servidor (ex.: inter-webhook-ca.crt). */
    public function clientCaFileName(): string;
}
