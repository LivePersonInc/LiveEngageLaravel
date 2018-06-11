<?php

namespace LivePersonInc\LiveEngageLaravel\Tests;

use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel;
use LivePersonInc\LiveEngageLaravel\ServiceProvider;
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
