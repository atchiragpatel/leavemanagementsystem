<?php

namespace App\Providers;

use App\Console\Commands\LeaveTransaction;
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
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    public function registerCommands()
    {
        $this->commands(LeaveTransaction::class);
    }
}
