<?php

namespace Faheem2407\RouteMaker;

use Illuminate\Support\ServiceProvider;

class RouteMakerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            Console\MakeRouteFile::class,
        ]);
    }

    public function boot()
    {
        
    }
}
