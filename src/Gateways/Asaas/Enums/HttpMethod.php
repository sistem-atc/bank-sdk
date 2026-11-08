<?php

namespace SistemAtc\Banks\Gateways\Asaas\Enums;

enum HttpMethod: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
}
