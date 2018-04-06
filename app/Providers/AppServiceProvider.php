<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        \View::share('channels', \App\Channel::all());

        \View::composer('*', function ($view) {
            $channels = \Cache::rememberForever('channel', function () {
                return \App\Channel::all();
            });
            $view->with('channels', $channels);
//            $view->with('channels', \App\Channel::all());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}