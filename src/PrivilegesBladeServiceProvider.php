<?php

namespace Panoscape\Privileges;

use Blade;
use Illuminate\Support\ServiceProvider;

class PrivilegesBladeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('validate', function ($value) {
            return "<?php if(app('auth')->user() instanceof Panoscape\Privileges\Privileged && app('auth')->user()->validate($value)) :  ?>";
        });        

        Blade::directive('endvalidate', function ($value) {
            return "<?php endif;  ?>";
        });

        Blade::directive('group', function ($value) {
            return "<?php if(app('auth')->user() instanceof Panoscape\Privileges\Privileged && app('auth')->user()->__groups()->validate($value)) :  ?>";
        });        

        Blade::directive('endgroup', function ($value) {
            return "<?php endif;  ?>";
        });

        Blade::directive('privilege', function ($value) {
            return "<?php if(app('auth')->user() instanceof Panoscape\Privileges\Privileged && app('auth')->user()->__privileges()->validate($value)) :  ?>";
        });        

        Blade::directive('endprivilege', function ($value) {
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