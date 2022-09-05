<?php

namespace Mamdouh\Iamshare;

use Illuminate\Support\ServiceProvider;

class IAMShareProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config' => app_path('../config'),
        ]);
    }
}
