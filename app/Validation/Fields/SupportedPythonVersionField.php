<?php declare(strict_types=1);

namespace App\Validation\Fields;

use App\Support\Arr;

class SupportedPythonVersionField extends AbstractField
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->lengthBetween(1, 10)
            ->in(Arr::keys(config('servers.python.versions')));
    }
}
