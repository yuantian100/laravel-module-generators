<?php namespace Yuan\ModuleGenerators;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Yuan\ModuleGenerators\Exceptions\ModuleExistsException;

class ModuleGenerator {

    protected $file;
    protected $config;
    protected $flags;
    protected $namespace;

    public function __construct(Filesystem $file, Config $config)
    {
        $this->file = $file;
        $this->config = $config;
    }

    /**
     * Bootstrap base model, controller...
     *
     * @param $namespace
     */
    public function bootstrap($namespace)
    {
        $this->setNamespace($namespace);
        $this->makeBaseServiceProvider();
        $this->makeBaseControllers();
        $this->makeBaseModel();
    }

    /**
     * generate a new module
     *
     * @param $module
     * @param $namespace
     *
     * @throws ModuleExistsException
     */
    public function generate($module, $namespace)
    {
        $this->setNamespace($namespace);
        $module = ucwords($module);
        if ($this->file->isDirectory(app_path() . '/Modules/' . $module))
        {
            throw New ModuleExistsException;
        }
        $types = [
            'makeServiceProvider',
            'makeControllers',
            'makeViews',
            'makeModels',
            'makeRoute',
            'makeLang',
            'makeInterface',
            'makeRepository',
            'makeConfig'
        ];

        foreach ($types as $type)
        {
            $this->{$type}($module);
        }
    }

    /**
     * Make folders and files
     *
     * @param $module
     * @param $path
     * @param $templatePath
     * @param $filename
     */
    public function make($module, $path, $templatePath, $filename)
    {
        // get the real path of the folder need to be created
        $folder = $this->getRealPath($module, $path);
        // create the folder
        $this->makeFolder($folder);
        // fetch template content
        $template = $this->getTemplate($templatePath);
        // replace flags in template
        $template = $this->replaceFlags($module, $path, $template);
        // set new file path
        $path = $folder . '/' . $filename;
        // create the file
        $this->file->put($path, $template);
    }

    public function makeBaseModel()
    {
        $module = ucwords($this->getConfig('base_module_name'));
        $filename = $module . '.php';
        $this->make($module, 'model_path', 'base_model_template_path', $filename);
    }


    public function makeBaseControllers()
    {
        $module = ucwords($this->getConfig('base_module_name'));
        $filename = $module . 'Controller.php';
        $this->make($module, 'controller_path', 'base_controller_template_path', $filename);
    }

    public function makeBaseServiceProvider()
    {
        // fetch template content
        $template = $this->getTemplate('base_service_provider_template_path');
        // replace flags in template
        $template = $this->replaceFlag($template, 'app.name', $this->getNamespace());
        // set new file path
        $path = app_path() . '/providers/ModulesServiceProvider.php';
        // create the file
        $this->file->put($path, $template);
    }

    public function makeRepository($module)
    {
        $filename = $module . 'Repository.php';
        $this->make($module, 'repository_path', 'repository_template_path', $filename);
    }

    public function makeInterface($module)
    {
        $filename = $module . 'Interface.php';
        $this->make($module, 'interface_path', 'interface_template_path', $filename);
    }

    public function makeServiceProvider($module)
    {
        $filename = $module . 'ServiceProvider.php';
        $this->make($module, 'service_provider_path', 'service_provider_template_path', $filename);
    }

    public function makeControllers($module)
    {
        $filename = $module . 'Controller.php';
        $this->make($module, 'controller_path', 'controller_template_path', $filename);
    }

    public function makeViews($module)
    {
        $filename = 'index.blade.php';
        $this->make($module, 'view_path', 'view_template_path', $filename);
    }

    public function makeModels($module)
    {
        $filename = $module . '.php';
        $this->make($module, 'model_path', 'model_template_path', $filename);
    }

    public function makeRoute($module)
    {
        $filename = 'router.php';
        $this->make($module, 'route_path', 'route_template_path', $filename);
    }

    public function makeLang($module)
    {
        $folder = $this->getRealPath($module, 'lang_path');
        $this->makeFolder($folder);
    }

    public function makeConfig($module)
    {
        $filename = 'config.php';
        $this->make($module, 'config_path', 'config_template_path', $filename);
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Replace the given string in the given file.
     *
     * @param  string $path
     * @param  string $search
     * @param  string $replace
     *
     * @return void
     */
    protected function replaceIn($path, $search, $replace)
    {
        $this->file->put($path, str_replace($search, $replace, $this->file->get($path)));
    }

    public function getPath($module, $path)
    {
        return $this->getNamespace() . '/Modules/' . $module . '/' . $this->getConfig($path);
    }

    public function getRealPath($module, $path)
    {
        return app_path() . '/Modules/' . $module . '/' . $this->getConfig($path);
    }

    public function getTemplate($type)
    {
        return $this->file->get(__DIR__ . '/' . $this->getConfig($type));
    }


    /**
     * replace all flags in template file
     *
     * @param $flags
     * @param $template
     *
     * @return mixed
     */
    public function replaceFlags($module, $path, $template)
    {
        $flags = $this->flags($module, $path);
        foreach ($flags as $flag => $replacement)
        {

            $template = $this->replaceFlag($template, $flag, $replacement);
        }

        return $template;
    }

    /**
     * set how the flags should be replaced in templates, key will be replaced by its value
     *
     * @param $module
     *
     * @return mixed
     */
    public function flags($module, $path)
    {
        $flags['module'] = $module;
        $flags['namespace'] = $this->changeSlash(trim($this->getPath($module, $path), '/'));
        $flags['base'] = $this->changeSlash(trim($this->getPath($this->getConfig('base_module_name'), $path), '/'));
        return $flags;
    }

    public function changeSlash($content)
    {
        return str_replace('/', '\\', $content);
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


    /**
     * Make folders
     *
     * @param array $paths
     *
     * @return void
     */
    public function makeFolders(array $paths)
    {
        foreach ($paths as $path)
        {
            $this->makeFolder($path);
        }
    }

    protected function makeFolder($path)
    {
        if (!$this->file->isDirectory($path))
        {

            $this->file->makeDirectory($path, 0777, true);
        }
    }

    protected function getConfig($config)
    {
        return $this->config->get("module-generators::config.{$config}");
    }
}