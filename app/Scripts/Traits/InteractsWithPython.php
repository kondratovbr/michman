<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use App\Scripts\Exceptions\ServerScriptException;

trait InteractsWithPython
{
    protected function verifyPythonWorks(string $version): void
    {
        if (trim($this->exec("python$version -c 'print(\"foobar\")'")) != 'foobar')
            throw new ServerScriptException("Python $version installation failed - Python not accessible.");
    }

    protected function getPythonVersion(string $version): string
    {
        return trim(explode(
            ' ',
            $this->exec("python$version --version"),
            2
        )[1]);
    }
}
