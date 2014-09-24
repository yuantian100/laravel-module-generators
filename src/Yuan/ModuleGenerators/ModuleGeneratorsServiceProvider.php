<?php namespace Yuan\ModuleGenerators;

use Illuminate\Support\ServiceProvider;

class ModuleGeneratorsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('yuan/module-generators');

    }

    public function register()
    {
        foreach ([
                     'Bootstrap',
                     'Generate',
                 ] as $command)
        {
            $this->{"register{$command}Command"}();
        }

    }

    public function registerBootstrapCommand()
    {

        $this->app['module.bootstrap'] = $this->app->share(function ($app)
        {
            return $app->make('Yuan\ModuleGenerators\Commands\ModuleBootstrapCommand');

        });
        $this->commands('module.bootstrap');
    }

    public function registerGenerateCommand()
    {

        $this->app['module.generate'] = $this->app->share(function ($app)
        {
            return $app->make('Yuan\ModuleGenerators\Commands\ModuleGenerateCommand');

        });
        $this->commands('module.generate');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
