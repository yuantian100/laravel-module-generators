<?php namespace Yuan\ModuleGenerators\Commands;

use Illuminate\Console\Command;
use Config;
class ModuleCommands extends Command {


    protected function getNamespace()
    {
        //$namespace = $this->laravel['config']['namespaces.root'];
        $namespace = Config::get("module-generators::config.namespace");
        return trim($namespace, '\\');
    }

    protected function isValidClassName($class)
    {
        return preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $class);
    }
}
