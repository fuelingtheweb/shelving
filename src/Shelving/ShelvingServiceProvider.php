<?php namespace Fuelingtheweb\Shelving;

use Illuminate\Support\ServiceProvider;

class ShelvingServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container
     */
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/shelving.php', 'shelving');
    }

    /**
     * Perform post-registration booting of services
     */
    public function boot() {
        $this->publishes([
            __DIR__ . '/../config/shelving.php' => config_path('shelving.php')
        ]);
    }
}
