<?php

namespace MadWeb\Initializer\Console\Commands;

use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use MadWeb\Initializer\Contracts\Runner as ExecutorContract;
use MadWeb\Initializer\Run;

abstract class AbstractInitializeCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Container $container)
    {
        $initializerInstance = null;

        try {
            $initializerInstance = $this->getInitializerInstance($container);
        } catch (ErrorException $e) {
            $this->error('Publish initializer classes:');
            $this->error('$ php artisan vendor:publish --tag=initializers');

            return;
        }

        /** @var ExecutorContract $Executor */
        $config = $container->make('config');
        $env = $config->get($config->get('initializer.env_config_key'));

        $this->alert($this->title().' started');

        $result = $initializerInstance
            ->{$this->option('root') ? $env.'Root' : $env}
            ($container->makeWith(Run::class, ['artisanCommand' => $this]));

        $this->output->newLine();

        if ($result->errorMessages()) {
            $this->line('<fg=red>'.$this->title().' done with errors:</>');

            $this->output->newLine();

            foreach ($result->errorMessages() as $message) {
                $this->error($message);
                $this->output->newLine();
            }

            $this->line('<fg=red>You could run command with <fg=cyan>-v</> flag to see more details</>');
        } else {
            $this->info($this->title().' done!');
        }
    }

    /**
     * Returns initializer instance for current command.
     *
     * @return object
     */
    abstract protected function getInitializerInstance(Container $container);

    abstract protected function title(): string;
}
