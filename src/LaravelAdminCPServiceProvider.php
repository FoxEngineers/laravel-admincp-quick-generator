<?php

namespace FoxEngineers\AdminCP;

use Closure;
use Illuminate\Support\ServiceProvider;

class LaravelAdminCPServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'laravel-admincp');
    }

    /**
     * Register all modules.
     */
    public function register()
    {
    }
}