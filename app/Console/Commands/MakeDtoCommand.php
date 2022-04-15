<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeDtoCommand extends GeneratorCommand
{
    protected $type = 'Data transfer object';

    /** @var string The name and signature of the console command. */
    protected $signature = 'make:dto {name}';

    /** @var string The console command description. */
    protected $description = 'Create a new data transfer object class';

    /** Get the stub file for the generator. */
    protected function getStub(): string
    {
        return base_path() . '/stubs/dto.stub';
    }

    /** Get the default namespace for the class. */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\DataTransferObjects';
    }
}
