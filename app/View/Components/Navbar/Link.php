<?php declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    public string|null $href;
    public string|null $routeName;
    public bool $disabled;
    public bool $active;
    public string $stateClasses;
    public string $contentStateClasses;

    // Classes to apply to the button itself
    private const INACTIVE_CLASSES = 'group-hover:border-opacity-100 group-hover:text-gray-100 bg-navy-400 bg-opacity-0 group-active:bg-opacity-100';
    private const ACTIVE_CLASSES = 'bg-navy-500 text-gray-100';

    // Classes to apply to the content of a button
    private const CONTENT_INACTIVE_CLASSES = 'group-hover:scale-110';
    private const CONTENT_ACTIVE_CLASSES = '';

    public function __construct(string $routeName = null, $href = null, $disabled = false)
    {
        $this->routeName = $routeName;
        $this->href = $href;
        $this->disabled = $disabled;
        $this->active = isset($routeName) && request()->routeIs($routeName);
        $this->stateClasses = $this->active ? self::ACTIVE_CLASSES : self::INACTIVE_CLASSES;
        $this->contentStateClasses = $this->active ? self::CONTENT_ACTIVE_CLASSES : self::CONTENT_INACTIVE_CLASSES;
    }

    public function render(): View
    {
        return view('components.navbar.link');
    }
}
