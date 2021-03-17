<?php declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public string $alignmentClasses;
    public string $widthClass;

    public function __construct(string $align = 'left', string $width = null, string $minWidth = '48')
    {
        $this->alignmentClasses = $this->alignmentClasses($align);
        $this->widthClass = $this->widthClass($width, $minWidth);
    }

    /**
     * Get the alignment classes for the dropdown menu.
     */
    private function alignmentClasses(string $align): string
    {
        return match ($align) {
            'left'          => 'origin-top-left left-0',
            'right'         => 'origin-top-right right-0',
            'top'           => 'origin-top',
            'none', 'false' => '',
        };
    }

    /**
     * Get the width class for the dropdown menu.
     */
    private function widthClass(?string $width, string $minWidth): string
    {
        $class = match ($width) {
            '48' => 'w-48',
            '60' => 'w-60',
            null => null,
        };

        $class ??= match ($minWidth) {
            '48' => 'min-w-48',
            '60' => 'min-w-60',
        };

        return $class;
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.navbar.dropdown');
    }
}
