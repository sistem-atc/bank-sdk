<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SistemAtc\Banks\BanksServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BanksServiceProvider::class,
        ];
    }
}
