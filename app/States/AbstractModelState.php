<?php declare(strict_types=1);

namespace App\States;

use App\Support\Arr;
use Spatie\ModelStates\State;
use RuntimeException;

abstract class AbstractModelState extends State
{
    public static string $name;
    public function is(string|array $states): bool
    {
        $abstract = get_parent_class($this);

        foreach (Arr::wrap($states) as $state) {
            if (! is_subclass_of($state, $abstract))
                throw new RuntimeException('A state provided extends a different model state. Provided: ' . $state . ', $this: ' . $this::class);

            if ($this instanceof $state)
                return true;
        }

        return false;
    }
}
