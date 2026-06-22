<?php

namespace App\Providers;

use App\Services\BusinessContext;
use App\Services\SettingsRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191); // Fix for older MySQL/MariaDB versions

        try {
            if (Schema::hasTable('businesses')) {
                View::share('currentBusiness', app(BusinessContext::class)->current());
            }

            if (Schema::hasTable('settings')) {
                View::share('globalSettings', app(SettingsRepository::class)->all());
            }
        } catch (\Throwable $e) {
            // Skip shared data when the database is not ready yet.
        }
    }
}
