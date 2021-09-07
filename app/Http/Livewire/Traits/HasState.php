<?php declare(strict_types=1);

namespace App\Http\Livewire\Traits;

use App\Support\Arr;
use Livewire\Component;

// TODO: Use this trait to slightly refactor the behaviour of the Livewire Components that use $state to store user input.

/**
 * Trait to ease the handling of user input with the $state property in Livewire components.
 *
 * @mixin Component
 */
trait HasState
{
    abstract protected function stateRules(): array;

    public function rules(): array
    {
        return Arr::mapAssoc($this->stateRules(), fn(string $name, $rules) => ["state.$name", $rules]);
    }
}
