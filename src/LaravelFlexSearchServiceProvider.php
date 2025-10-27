<?php

namespace DaiyanMozumder\LaravelFlexSearch;

use Illuminate\Support\ServiceProvider;

class LaravelFlexSearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('flex-search', function () {
            return new FlexSearch();
        });
    }

    public function boot()
    {

    }
}
