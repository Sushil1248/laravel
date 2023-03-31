<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Alert;
use Illuminate\Pagination\Paginator;
use App\Channels\DatabaseChannel;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

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
        // Blade::component('alert', Alert::class,'alert');
        // Blade::component('admin.components.alert', 'alert');
        Paginator::useBootstrapFour();
        Blade::component('admin.components.daterange', 'daterangeScipts');
        Schema::defaultStringLength(191);
        $this->app->instance(IlluminateDatabaseChannel::class, new DatabaseChannel);
    }
}
