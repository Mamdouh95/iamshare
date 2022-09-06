<?php

namespace Mamdouh\Iamshare;

use Illuminate\Support\ServiceProvider;

class IAMShareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/app' => app_path('../app'),
            __DIR__ . '/config' => app_path('../config'),
        ]);
    }
}
