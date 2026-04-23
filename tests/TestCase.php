<?php

namespace RadicalDreamers\UsptoClient\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RadicalDreamers\UsptoClient\USPTOServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [USPTOServiceProvider::class];
    }
}
