<?php declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    private const ACTIVE_CLASSES = 'bg-navy-500 text-gray-100';
    private const INACTIVE_CLASSES = 'group-hover:border-opacity-100 group-hover:text-gray-100 bg-navy-400 bg-opacity-0 group-active:bg-opacity-100';

    public string $routeName;
    public bool $active;
    public string $stateClasses;

    public function __construct(string $routeName)
    {
        $this->routeName = $routeName;
        $this->active = request()->routeIs($routeName);
        $this->stateClasses = $this->active ? self::ACTIVE_CLASSES : self::INACTIVE_CLASSES;
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.navbar.link');
    }
}
