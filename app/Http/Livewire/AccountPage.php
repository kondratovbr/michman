<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Support\Arr;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AccountPage extends Component
{
    /** @var string Currently shown sub-page. */
    public string $show = '';

    protected $queryString = ['show'];

    /** @var string[] Map of $show property values to sub-page views. */
    private const VIEWS = [
        'profile' => 'profile.show',
        'foobar' => 'foobar',
    ];

    /**
     * Prepare the component.
     */
    public function mount(string $show = 'profile'): void
    {
        $this->show = $show ?? self::VIEWS[0];
    }

    /**
     * Change a currently shown page.
     */
    public function show(string $show): void
    {
        $this->show = $show;
    }

    /**
     * Get the name of the view for the currently shown page.
     */
    public function getPageProperty(): string
    {
        return self::VIEWS[$this->show];
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        if (! Arr::has(self::VIEWS, $this->show))
            abort(404);

        return view('livewire.account-page');
    }
}
