<?php namespace Yuan\ModuleGenerators\Commands;

use Illuminate\Console\Command;


class ModuleCommands extends Command {

    protected function getNamespace()
    {
        $namespace = $this->laravel['config']['namespaces.root'];
        return trim($namespace, '\\');
    }
}
