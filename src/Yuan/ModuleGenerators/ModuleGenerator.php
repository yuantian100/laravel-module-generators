<?php namespace Yuan\ModuleGenerators;

use Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class ModuleGenerator {

    protected $paths;
    protected $templates;
    protected $basePath;
    protected $module;
    protected $namespace;
    protected $file;


    public function __construct(Filesystem $file)
    {
        $this->file = $file;
    }

    private function pathToModuleRoot()
    {
        return app_path() . '/Modules/' . $this->getModule();
    }

    protected function getConfig($config)
    {
        return Config::get("module-generators::config.{$config}");
    }

    protected $dirs = [
        'controller',
        'view',
        'model',
        'presenter',
        'lang',
        'repository',
        'config',
        'event',
    ];

    private function setPaths()
    {
        $moduleRoot = $this->pathToModuleRoot();
        foreach ($this->dirs as $dir)
        {
            $this->paths[$dir] = $moduleRoot . '/' . $this->getConfig($dir);
        }
    }


    public function getTemplates()
    {
        $templatesRoot = $this->getTemplatePath();
        $templates['view'] = $templatesRoot . 'view.tpl';
        $templates['config'] = $templatesRoot . 'config.tpl';
        $templates['model'] = $templatesRoot . 'model.tpl';
        $templates['controller'] = $templatesRoot . 'controller.tpl';
        $templates['lang'] = $templatesRoot . 'lang.tpl';
        $templates['route'] = $templatesRoot . 'route.tpl';
        $templates['service'] = $templatesRoot . 'serviceProvider.tpl';
        $templates['repositoryInterface'] = $templatesRoot . 'repositoryInterface.tpl';
        $templates['repository'] = $templatesRoot . 'repository.tpl';
        return $templates;
    }


    private function getPaths()
    {
        return $this->paths;
    }

    private function setModule($module)
    {
        $this->module = $module;
    }

    private function getModule()
    {
        return $this->module;
    }


    public function make($module, $namespace)
    {
        $this->setModule($module);
        $this->setNamespace($namespace);

        $this->setPaths();

        $this->makeDirectory();
        $this->makeFiles();
    }

    private function makeDirectory()
    {
        $paths = $this->getPaths();
        foreach ($paths as $path)
        {
            File::makeDirectory($path, 0777, true);
        }

    }

    private function makeFiles()
    {
        $this->makeServiceProvider();
        $this->makeRoute();
        $this->makeViews();
        $this->makeConfig();
        $this->makeModel();
        $this->makeController();
        $this->makeLang();
        $this->makeRepository();
    }

    private function makeViews()
    {
        $module = $this->getModule();
        $path = $this->getPaths()['view'] . '/index.blade.php';
        $template = $this->file->get($this->getTemplates()['view']);
        $template = str_replace('{{module}}', $module, $template);
        $this->file->put($path, $template);
    }

    public function makeConfig()
    {
        $path = $this->getPaths()['config'] . '/config.php';
        $template = $this->file->get($this->getTemplates()['config']);
        $this->file->put($path, $template);
    }

    private function makeServiceProvider()
    {
        $module = ucwords($this->getModule());
        $namespace = $this->getNamespace();
        $namespace = "{$namespace}\\Modules\\{$module}";
        $path = $this->pathToModuleRoot() . '/' . $this->getModule() . 'ServiceProvider.php';
        $template = $this->file->get($this->getTemplates()['service']);
        $template = str_replace('{{namespace}}', $namespace, $template);
        $template = str_replace('{{module}}', $module, $template);
        $this->file->put($path, $template);

    }

    private function makeModel()
    {
        $module = ucwords($this->getModule());
        $namespace = $this->getNamespace();
        $path = $this->getPaths()['model'] . '/' . $module . '.php';
        $template = $this->file->get($this->getTemplates()['model']);
        $namespace = "{$namespace}\\Modules\\{$module}\\Models";
        $template = str_replace('{{namespace}}', $namespace, $template);
        $template = str_replace('{{module}}', $module, $template);
        $this->file->put($path, $template);
    }

    private function makeController()
    {
        $module = ucwords($this->getModule());
        $namespace = $this->getNamespace();
        $path = $this->getPaths()['controller'] . '/' . $module . 'Controller.php';
        $template = $this->file->get($this->getTemplates()['controller']);
        $namespace = "{$namespace}\\Modules\\{$module}\\Controllers";
        $template = str_replace('{{namespace}}', $namespace, $template);
        $template = str_replace('{{module}}', $module, $template);
        $this->file->put($path, $template);
    }

    private function makeLang()
    {
        $path = $this->getPaths()['lang'] . '/index.php';
        $template = $this->file->get($this->getTemplates()['lang']);
        $this->file->put($path, $template);
    }

    private function makeRoute()
    {
        $module = ucwords($this->getModule());
        $namespace = $this->getNamespace();
        $namespace = "{$namespace}\\Modules\\{$module}\\Controllers";
        $path = $this->pathToModuleRoot() . '/router.php';
        $template = $this->file->get($this->getTemplates()['route']);
        $template = str_replace('{{namespace}}', $namespace, $template);
        $this->file->put($path, $template);
    }


    private function setNamespace($namespace)
    {
        $namespace = $namespace ? $namespace : 'App';
        $this->namespace = $namespace;
    }

    private function getNamespace()
    {
        return $this->namespace;
    }

    private function getTemplatePath()
    {
        return __DIR__ . '/templates/';
    }

    private function makeRepository()
    {
        $module = ucwords($this->getModule());
        $namespace = $this->getNamespace();
        $path = $this->getPaths()['repository'] . '/' . $module . 'Repository.php';
        $template = $this->file->get($this->getTemplates()['repository']);
        $namespace = "{$namespace}\\Modules\\{$module}\\Repositories";
        $template = str_replace('{{namespace}}', $namespace, $template);
        $template = str_replace('{{module}}', $module, $template);
        $this->file->put($path, $template);

        $namespace = $this->getNamespace();
        $path = $this->getPaths()['repository'] . '/' . $module . 'Interface.php';
        $template = $this->file->get($this->getTemplates()['repositoryInterface']);
        $namespace = "{$namespace}\\Modules\\{$module}\\Repositories";
        $template = str_replace('{{namespace}}', $namespace, $template);
        $template = str_replace('{{module}}', $module, $template);
        $this->file->put($path, $template);

    }


} 