<?php

namespace DoeAnderson\HorizonCheck\Tests;

use DoeAnderson\HorizonCheck\HorizonCheckServiceProvider;
use Laravel\Horizon\HorizonServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            HorizonServiceProvider::class,
            HorizonCheckServiceProvider::class,
        ];
    }
}
