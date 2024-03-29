<?php declare(strict_types=1);

namespace App\States;

use App\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\State;
use RuntimeException;

abstract class AbstractModelState extends State
{
    public static string $name;

    /**
     * Check that this state is one of the states provided.
     *
     * @param string|string[] $states
     */
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

    /** Override the built-in method to prevent transitioning from a state to the same state. */
    public function canTransitionTo($newState, ...$transitionArgs): bool
    {
        if ($this instanceof $newState || static::$name === $newState)
            return false;

        return parent::canTransitionTo($newState, $transitionArgs);
    }

    /**
     * Check that the requested transition is possible and perform it.
     * Silently do nothing if it isn't possible.
     */
    public function transitionToIfCan($newState, ...$transitionArgs): Model
    {
        if ($this->canTransitionTo(...func_get_args()))
            $this->transitionTo(...func_get_args());

        return $this->getModel();
    }
}
