<?php namespace {{app.name}}\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider {

    public function register()
    {

        $this->app->register('Fuckjob\Modules\User\UserServiceProvider');

    }
}