<?php

namespace App\Providers;

use App\Console\Commands\LeaveTransaction;
use App\Http\Validators\LeaveValidator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootValidator();
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

    public function bootValidator()
    {
        Validator::extend('is_valid_leave', LeaveValidator::class . '@handle',
            "Please Enter One Primary Contact Detail");
        Validator::extend('is_sandwitch', LeaveValidator::class . '@isSandwitch',
            "You are in a sandwitch rule");
    }

    public function registerCommands()
    {
        $this->commands(LeaveTransaction::class);
    }
}
