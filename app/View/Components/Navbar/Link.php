<?php declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    private const ACTIVE_CLASSES = 'border-gray-300 focus:outline-none focus:border-indigo-700';
    private const INACTIVE_CLASSES = 'border-transparent hover:text-gray-100 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300';

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
