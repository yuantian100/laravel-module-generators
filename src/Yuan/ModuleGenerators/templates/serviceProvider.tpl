<?php namespace {{namespace}};

use Illuminate\Support\ServiceProvider;
use View;
use Lang;
use Config;

class {{module}}ServiceProvider extends ServiceProvider {

    function boot()
    {
        // add routes
        require __DIR__ . '/router.php';
        View::addNamespace('{{module}}', __DIR__ . '/Views');
        // use User::... to get translation
        Lang::addNamespace('{{module}}', __DIR__ . '/Lang');
        Config::addNamespace('{{module}}', __DIR__ . '/Config');
    }

    public function register()
    {
        $app = $this->app;

        // Bind the {{module}} Repository
        $app->bind('{{namespace}}\Repositories\{{module}}Interface', '{{namespace}}\Repositories\{{module}}Repository');

    }

} 