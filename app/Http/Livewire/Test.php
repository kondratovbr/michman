<?php declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Test extends Component
{
    public Collection $foobars;

    public function mount(): void
    {
        $this->foobars = collect([
            1 => 'foo',
            22 => 'bar',
            3 => 'baz',
        ]);
    }

    public function render(): string
    {
        return <<<BLADE
<div>
    <button wire:click="doABarrelRoll">Press me!</button>
</div>
BLADE;
    }
}
