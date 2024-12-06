<?php

namespace Oilytortoise\LaravelTelemetry;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider to load the telemetry service when
 * the laravel app boots up.
 * 
 * @author OilyTortoise
 * @since 06 Dec 2024
 */
class TelemetryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishConfig();
    }

    /**
     * Publish the package's config files
     */
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/telemetry.php' => config_path('telemetry.php'),
        ], 'telemetry-config');
    }
}