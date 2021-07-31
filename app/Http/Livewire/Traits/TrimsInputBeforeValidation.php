<?php declare(strict_types=1);

namespace App\Http\Livewire\Traits;

use App\Support\Arr;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Livewire\Component as LivewireComponent;
use Livewire\ComponentConcerns\ValidatesInput;

/**
 * Trait TrimsInputBeforeValidation for Livewire input form components.
 *
 * Overrides the built-in getDataForValidation method to also trim
 * input strings and convert the empty ones to null.
 *
 * @mixin LivewireComponent
 * @mixin ValidatesInput
 */
trait TrimsInputBeforeValidation
{
    /** @var string[] Attributes to not trim before validation. In dot notation. */
    protected array $doNotTrim = [
        //
    ];

    /** @var string[] Attributes to keep as strings after trimming even if they are empty. In dot notation. */
    protected array $keepEmptyStrings = [
        //
    ];

    /**
     * Overrides the built-in method to also trim input strings
     * before preparing attributes for validation.
     */
    protected function getDataForValidation($rules): array
    {
        $properties = $this->getPublicPropertiesDefinedBySubClass();

        collect($rules)->keys()
            ->each(function ($ruleKey) use ($properties) {
                $propertyName = $this->beforeFirstDot($ruleKey);

                throw_unless(
                    array_key_exists($propertyName, $properties),
                    new \Exception('No property found for validation: ['.$ruleKey.']')
                );
            });

        $data = collect($properties)->map(function ($value) {
            if ($value instanceof Collection || $value instanceof EloquentCollection)
                return $value->toArray();

            return $value;
        })->all();

        return $this->trimData($data);
    }

    /**
     * Trim the input data that should be validated later.
     */
    private function trimData(array $attributes): array
    {
        $attributes = Arr::dot($attributes);

        foreach ($attributes as $key => $value) {
            if (
                $this->hasRuleFor($key)
                && ! in_array($key, $this->doNotTrim)
            ) {
                $attributes[$key] = $this->trimAttribute($key, $value);
            }
        }

        $attributes = Arr::undot($attributes);

        return $attributes;
    }

    /**
     * Trim a string and null if empty.
     */
    private function trimAttribute(string $attribute, mixed $value): mixed
    {
        if (! is_string($value))
            return $value;

        $value = trim($value);

        if (in_array($attribute, $this->keepEmptyStrings))
            return $value;

        return $value === '' ? null : $value;
    }
}
