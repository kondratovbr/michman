<?php declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Test2 extends Component
{
    public array $foobars = [
        'foo' => 'FOO',
        'bar' => 'BAR',
        'baz' => 'BAZ',
    ];

    public array $state = [
        'stuff' => 'Stuff',
        'moreStuff' => 'More Stuff',
    ];

    public function doABarrelRoll(): void
    {
        //
    }

    public function render(): View
    {
        return view('livewire.test2');
    }
}
