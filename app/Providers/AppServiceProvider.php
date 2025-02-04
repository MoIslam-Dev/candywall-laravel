<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            Request::setTrustedProxies(
                ['REMOTE_ADDR'],
                Request::HEADER_X_FORWARDED_FOR
            );
        }
    }
}
