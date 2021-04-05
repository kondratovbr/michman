<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Support\Arr;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AccountView extends Component
{
    /** @var string Currently shown sub-page. */
    public string $show = '';

    /** @var string[] The component's listeners. */
    protected $listeners = ['showSubPage' => 'show'];

    /** @var string[] Map of $show property values to sub-page views. */
    private const VIEWS = [
        'profile' => 'profile.show',
        'foobar' => 'foobar',
    ];

    /** @var string The name of a sub-page that will be shown by default. */
    private const DEFAULT_SHOW = 'profile';

    /**
     * Prepare the component.
     */
    public function mount(string $show = null): void
    {
        $this->show = $show ?? self::DEFAULT_SHOW;
    }

    /**
     * Get the name of the view for the currently shown page.
     */
    public function getPageProperty(): string
    {
        return self::VIEWS[$this->show];
    }

    /**
     * Change a currently shown page.
     */
    public function show(string $show): void
    {
        $this->show = $show;
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        if (! Arr::has(self::VIEWS, $this->show))
            abort(404);

        return view('account.show')->layout('layouts.app-with-menu');
    }
}
