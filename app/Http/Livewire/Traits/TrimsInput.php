<?php declare(strict_types=1);

namespace App\Http\Livewire\Traits;

/**
 * Trait TrimsInput for Livewire input form components.
 */
trait TrimsInput
{
    /** @var string[] */
    protected array $convertEmptyStringsExcept = [
        //
    ];

    /**
     * Trim updated input values and convert empty strings to null.
     *
     * Supports standard dot notation.
     * Called automatically by the Livewire lifecycle hooks.
     */
    public function updatedConvertEmptyStringsToNull(string $name, $value): void
    {
        if (! is_string($value) || in_array($name, $this->convertEmptyStringsExcept)) {
            return;
        }

        $value = trim($value);
        $value = $value === '' ? null : $value;

        // Set the new value using Laravel's dot notation.
        data_set($this, $name, $value);
    }
}
