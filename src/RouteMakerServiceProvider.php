<?php

namespace Faheem2407\RouteMaker;

use Illuminate\Support\ServiceProvider;
use Faheem2407\RouteMaker\Console\MakeRouteFile;

class RouteMakerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeRouteFile::class,
        ]);
    }

    public function boot()
    {
        //
    }
}
