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

    abstract protected function prepareState(array $state): array;

    public function rules(): array
    {
        return Arr::mapAssoc($this->stateRules(), fn(string $name, $rules) => ["state.$name", $rules]);
    }

    protected function prepareForValidation($attributes): array
    {
        $attributes['state'] = $this->prepareState($attributes['state']);
        return $attributes;
    }

    protected function validateState(): array
    {
        return $this->validate()['state'];
    }
}
