<?php

namespace LivePersonInc\LiveEngageLaravel\Tests;

use Orchestra\Testbench\TestCase;
use LivePersonInc\LiveEngageLaravel\ServiceProvider;
use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel;

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
