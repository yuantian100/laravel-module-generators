<?php namespace Yuan\ModuleGenerators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Yuan\ModuleGenerators\Exceptions\ModuleExistsException;
use Yuan\ModuleGenerators\ModuleGenerator;

class ModuleGenerateCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Module';
    /**
     * @var ModuleGenerator
     */
    private $controllerGenerator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ModuleGenerator $moduleGenerator)
    {
        parent::__construct();
        $this->moduleGenerator = $moduleGenerator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $module = $this->argument('module');
        try
        {
            $this->moduleGenerator->generate($module);
            $this->info("{$module} module has been created successfully");

        } catch (ModuleExistsException $e)
        {
            $this->info("{$module} module already has");
        }
//        $namespace = $this->argument('namespace');
//        $this->moduleGenerator->make($module, $namespace);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('module', InputArgument::REQUIRED, 'model'),
            array('namespace', InputArgument::OPTIONAL, 'namespace'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
