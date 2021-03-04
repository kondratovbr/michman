<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Exceptions\MethodNotFoundException;
use Illuminate\Console\Command;

abstract class AbstractCommand extends Command
{
    /**
     * A wrapper for any inherited command.
     */
    public function handle(): int
    {
        if (! method_exists($this, 'perform'))
            throw new MethodNotFoundException('Inherited console commands must have a "perform" method for running the actual command.');

        if ($this->isForbiddenOnProduction() && $this->isInProduction()) {
            $this->error('This command cannot be run in production!');
            return 1;
        }

        if (! $this->productionProtectionPassed()) {
            $this->comment('Command cancelled!');
            return 1;
        }

        return $this->getLaravel()->call([$this, 'perform']);
    }

    protected function isForbiddenOnProduction(): bool
    {
        return false;
    }

    protected function isProtectedOnProduction(): bool
    {
        return false;
    }

    private function isInProduction(): bool
    {
        return $this->getLaravel()->environment() === 'production';
    }

    private function productionProtectionPassed(): bool
    {
        if ( ! $this->isInProduction() || ! $this->isProtectedOnProduction())
            return true;

        if ($this->hasOption('force') && $this->option('force'))
            return true;

        $this->alert('The application is in production!');

        return $this->confirm('Are you sure you want to run this command on production?');
    }
}
