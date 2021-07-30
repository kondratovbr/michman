<?php declare(strict_types=1);

namespace App\Http\Livewire\Traits;

use RuntimeException;

/**
 * Trait TrimsInput for Livewire input form components.
 */
trait TrimsInput
{
    /*
     * TODO: CRITICAL! Turns out this trait has never worked - the method never got called at all.
     *       Trimming in this manner is wrong anyway - the values that aren't marked ".defer" get trimmed
     *       right as the user types, which is super annoying.
     *       Should go through the components that use this trait and trim only before validation or other usage instead.
     */

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
        throw new RuntimeException('This method is broken and should not be called at all. Fix!');

        if (! is_string($value) || in_array($name, $this->convertEmptyStringsExcept)) {
            return;
        }

        $value = trim($value);
        $value = $value === '' ? null : $value;

        // Set the new value using Laravel's dot notation.
        data_set($this, $name, $value);
    }
}
