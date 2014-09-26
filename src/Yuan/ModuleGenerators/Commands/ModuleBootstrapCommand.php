<?php namespace Yuan\ModuleGenerators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Yuan\ModuleGenerators\Exceptions\ModuleExistsException;
use Yuan\ModuleGenerators\ModuleGenerator;

class ModuleBootstrapCommand extends ModuleCommands {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:bootstrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up base model, controller and service provider';
    /**
     * @var ModuleGenerator
     */
    protected $moduleGenerator;

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
        try
        {
            $namespace = $this->getNamespace();
            $this->moduleGenerator->bootstrap($namespace);
            $this->info("Base module have been created successfully");
        } catch (ModuleExistsException $e)
        {
            $this->info("You already run bootstrap");
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
            array('namespace', InputArgument::OPTIONAL, 'the namespace of your project'),
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
