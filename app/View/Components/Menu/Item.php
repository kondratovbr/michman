<?php declare(strict_types=1);

namespace App\View\Components\Menu;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Item extends Component
{
    public string $show;
    public bool $active;
    public string $buttonStateClasses;
    public string $contentStateClasses;

    // Classes to apply to the button itself
    private const BUTTON_INACTIVE_CLASSES = 'bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100';
    private const BUTTON_ACTIVE_CLASSES = 'bg-opacity-100';

    // Classes to apply to the content of a button
    private const CONTENT_INACTIVE_CLASSES = 'group-hover:text-gray-100 group-hover:scale-105';
    private const CONTENT_ACTIVE_CLASSES = '';

    public function __construct(string $show, string $shownPage = '')
    {
        $this->show = $show;
        $this->active = $this->show === $shownPage;
        $this->buttonStateClasses = $this->active
            ? self::BUTTON_ACTIVE_CLASSES
            : self::BUTTON_INACTIVE_CLASSES;
        $this->contentStateClasses = $this->active
            ? self::CONTENT_ACTIVE_CLASSES
            : self::CONTENT_INACTIVE_CLASSES;
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.menu.item');
    }
}
