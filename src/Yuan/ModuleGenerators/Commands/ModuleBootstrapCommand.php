<?php namespace Yuan\ModuleGenerators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Yuan\ModuleGenerators\Exceptions\ModuleExistsException;
use Yuan\ModuleGenerators\ModuleGenerator;

class ModuleBootstrapCommand extends Command {

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
    private $moduleGenerator;

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
            $this->moduleGenerator->bootstrap();
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
        return array(//            array('example', InputArgument::REQUIRED, 'An example argument.'),
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
