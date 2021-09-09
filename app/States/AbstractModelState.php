<?php declare(strict_types=1);

namespace App\States;

use App\Support\Arr;
use Spatie\ModelStates\State;

abstract class AbstractModelState extends State
{
    public function is(string|array $states): bool
    {
        foreach (Arr::wrap($states) as $state) {
            if ($this instanceof $state)
                return true;
        }

        return false;
    }
}
