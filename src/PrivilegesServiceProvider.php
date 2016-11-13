<?php

namespace Panoscape\Privileges;

use Illuminate\Support\ServiceProvider;

class PrivilegesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/privileges_profile.php' => config_path('privileges_profile.php')
        ], 'profile');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/privileges_profile.php', 'privileges_profile');
    }
}