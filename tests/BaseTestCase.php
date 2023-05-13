<?php

namespace FoxEngineers\AdminCP\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
abstract class BaseTestCase extends OrchestraTestCase
{
    private function resetDatabase(): void
    {
        $this->artisan('migrate:reset', [
            '--database' => 'sqlite',
        ]);
    }

    /**
     * Set up the environment.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.asset_url', null);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}