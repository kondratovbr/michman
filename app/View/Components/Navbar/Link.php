<?php declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    public string|null $routeName;
    public bool $active;
    public string $stateClasses;
    public string $contentStateClasses;

    // Classes to apply to the button itself
    private const INACTIVE_CLASSES = 'group-hover:border-opacity-100 group-hover:text-gray-100 bg-navy-400 bg-opacity-0 group-active:bg-opacity-100';
    private const ACTIVE_CLASSES = 'bg-navy-500 text-gray-100';

    // Classes to apply to the content of a button
    private const CONTENT_INACTIVE_CLASSES = 'group-hover:scale-110';
    private const CONTENT_ACTIVE_CLASSES = '';

    public function __construct(string $routeName = null)
    {
        $this->routeName = $routeName;
        $this->active = isset($routeName) ? request()->routeIs($routeName) : false;
        $this->stateClasses = $this->active ? self::ACTIVE_CLASSES : self::INACTIVE_CLASSES;
        $this->contentStateClasses = $this->active ? self::CONTENT_ACTIVE_CLASSES : self::CONTENT_INACTIVE_CLASSES;
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.navbar.link');
    }
}
