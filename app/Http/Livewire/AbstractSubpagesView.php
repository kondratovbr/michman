<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Support\Arr;
use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

abstract class AbstractSubpagesView extends LivewireComponent
{
    /** @var string Base Blade view to render this page. */
    protected const VIEW = '';
    /** @var string Layout to use for rendering this page. */
    protected const LAYOUT = '';

    /** @var string Currently shown sub-page. */
    public string $show = '';

    /** @var string[] */
    protected $listeners = ['showSubPage' => 'show'];

    /** @var string[] Map of $show property values to sub-page Blade views. */
    public const VIEWS = [
        //
    ];

    /** @var string The name of a sub-page that will be shown by default. */
    protected const DEFAULT_SHOW = '';

    /**
     * Initialize the component.
     */
    public function mount(string $show = null): void
    {
        $this->show = $show ?? static::DEFAULT_SHOW;
    }

    /**
     * Get the name of the view for the currently shown page.
     */
    public function getPageProperty(): string
    {
        return static::VIEWS[$this->show];
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
        /*
         * If the requested sub-page isn't declared,
         * redirect the user to 404 page.
         * abort(404) doesn't work as intended here - it renders the error page in
         * the default Livewire modal window.
         */
        if (! Arr::has(static::VIEWS, $this->show))
            $this->redirect(route('error.404'));

        return view(static::VIEW)->layout(static::LAYOUT);
    }
}