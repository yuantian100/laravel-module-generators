<?php namespace Yuan\ModuleGenerators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Yuan\ModuleGenerators\Exceptions\ModuleExistsException;
use Yuan\ModuleGenerators\ModuleGenerator;

class ModuleGenerateCommand extends ModuleCommands {

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
        if ($this->isValidClassName($module))
        {
            try
            {
                $namespace = $this->getNamespace();
                $this->moduleGenerator->generate($module, $namespace);
                $this->info("{$module} module has been created successfully");

            } catch (ModuleExistsException $e)
            {
                $this->info("{$module} module already has");
            }
        } else
        {
            $this->error('Module can not be crated, because module name is invalid');
        }
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
