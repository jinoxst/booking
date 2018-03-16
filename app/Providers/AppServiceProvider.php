<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // DB::listen(function ($query) {
            // Log::info($query->sql);
            // app('sql-log')->debug($query->sql);
            // $query->sql
            // $query->bindings
            //Log::info($query->bindings);
            // app('sql-log')->debug($query->bindings);
            // $query->time
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
