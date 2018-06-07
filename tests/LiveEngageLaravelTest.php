<?php

namespace LivePersonNY\LiveEngageLaravel\Tests;

use LivePersonNY\LiveEngageLaravel\Facades\LiveEngageLaravel;
use LivePersonNY\LiveEngageLaravel\ServiceProvider;
use Orchestra\Testbench\TestCase;

class LiveEngageLaravelTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'live-engage-laravel' => LiveEngageLaravel::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
