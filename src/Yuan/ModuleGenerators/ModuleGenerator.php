<?php namespace Yuan\ModuleGenerators;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

class ModuleGenerator {

    protected $paths;
    protected $templates;
    protected $basePath;
    protected $module;
    protected $namespace;
    protected $file;
    protected $flags;
    protected $config;

    public function __construct(Filesystem $file, Config $config)
    {
        $this->file = $file;
        $this->config = $config;
    }

    public function bootstrap()
    {
        $directories = [
            'Base1/Controllers',
            'Base1/Models'
        ];
        $this->makeControllers('Base1', $directories, 'controller.tpl');
    }

    public function generate($module)
    {
        $directories = [
            "{$module}/Controllers"
        ];
        $this->makeControllers($module, $directories, 'controller.tpl');

        $directories = [
            "{$module}/Views"
        ];

        $this->makeViews($module, $directories, 'view.tpl');
    }
    public function makeViews($module, $directories, $path){

        // make directories for view
        $this->makeDirectories($directories);
        // fetch template content
        $template = $this->getTemplate($path);
        // set replace flags
        $flags['module'] = $module;
        $flags['namespace'] = 'App\Modules\\' . $module . 'Views';
        // replace these flags
        $template = $this->replaceFlags($flags, $template);
        // set new file path
        $path = $this->pathToModuleRoot($module) . $module . '/Views/index.blade.php';
        // create the controller
        $this->file->put($path, $template);

    }


    /**
     * make a controller
     *
     * @param $module
     * @param $directories
     * @param $path
     */
    public function makeControllers($module, $directories, $path)
    {
        // make directories for controller
        $this->makeDirectories($directories);
        // fetch template content
        $template = $this->getTemplate($path);
        // set replace flags
        $flags['module'] = $module;
        $flags['namespace'] = 'App\Modules\\' . $module . 'Controllers';
        // replace these flags
        $template = $this->replaceFlags($flags, $template);
        // set new file path
        $path = $this->pathToModuleRoot($module) . $module . '/Controllers/' . $module . 'Controller.php';
        // create the controller
        $this->file->put($path, $template);
    }

    /**
     * replace all flags in template file
     *
     * @param $flags
     * @param $template
     *
     * @return mixed
     */
    public function replaceFlags($flags, $template)
    {

        foreach ($flags as $flag => $replacement)
        {

            $template = $this->replaceFlag($template, $flag, $replacement);
        }

        return $template;
    }

    /**
     * replace a flag in template file
     *
     * @param $template
     * @param $search
     * @param $replace
     *
     * @return mixed
     */
    public function replaceFlag($template, $search, $replace)
    {

        return str_replace("{{{$search}}}", $replace, $template);

    }

    public function getTemplate($path)
    {
        return $this->file->get(__DIR__ . '/templates/' . $path);
    }


    /**
     * Make Directories
     *
     * @param array $paths
     *
     * @return void
     */
    public function makeDirectories(array $paths)
    {
        foreach ($paths as $path)
        {
            $this->file->makeDirectory(app_path() . '/Modules/' . $path, 0777, true);
        }

    }


    private function pathToModuleRoot()
    {
        return app_path() . '/Modules/' . $this->getModule();
    }

    protected function getConfig($config)
    {
        return $this->config->get("module-generators::config.{$config}");
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
            $this->file->makeDirectory($path, 0777, true);
        }

    }

    private function makeFiles()
    {
        $this->makeServiceProvider();
        $this->makeRoute();
//        $this->makeViews();
        $this->makeConfig();
        $this->makeModel();
        $this->makeController();
        $this->makeLang();
        $this->makeRepository();
    }

//    private function makeViews()
//    {
//        $module = $this->getModule();
//        $path = $this->getPaths()['view'] . '/index.blade.php';
//        $template = $this->file->get($this->getTemplates()['view']);
//        $template = str_replace('{{module}}', $module, $template);
//        $this->file->put($path, $template);
//    }

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