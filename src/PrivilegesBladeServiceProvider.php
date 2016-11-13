<?php

namespace Panoscape\Privileges;

use Blade;

class PrivilegesBladeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('privileges', function ($pattern) {
            return "<?php if(($user = app('auth')->user()) && method_exists($user, 'validate') && $user->validate($pattern)) :  ?>";
        });        

        Blade::directive('endprivileges', function ($pattern) {
            return "<?php endif;  ?>";
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}