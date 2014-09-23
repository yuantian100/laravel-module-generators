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
        $this->commands('Yuan\ModuleGenerators\ModuleGeneratorCommand');
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
