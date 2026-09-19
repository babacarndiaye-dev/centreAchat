<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            if (Schema::hasTable('settings')) {
                if ($timezone = Setting::get('timezone')) {
                    date_default_timezone_set($timezone);
                    config(['app.timezone' => $timezone]);
                }
            }
        } catch (\Throwable) {
            // Database not ready (e.g. before first migration) — ignore.
        }
    }
}
