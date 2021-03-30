<?php declare(strict_types=1);

namespace App\View\Components\Dropdown;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Menu extends Component
{
    public string $show;
    public string $alignmentClasses;
    public string $widthClass;
    public string $marginClasses;

    public function __construct(
        string $show = 'open',
        string $drop = 'down',
        string $align = 'left',
        string $width = null,
        string $minWidth = '48'
    ) {
        $this->show = $show;
        $this->alignmentClasses = $this->alignmentClasses($drop, $align);
        $this->widthClass = $this->widthClass($width, $minWidth);
        $this->marginClasses = $this->marginClasses($drop);
    }

    /**
     * Get the alignment classes for the dropdown menu.
     */
    private function alignmentClasses(string $drop, string $align): string
    {
        // "origin" is for making the open/close transition nicer,
        // "left"/"right" is to align the menu with its button.
        $result = match ([$drop, $align]) {
            ['down', 'left']    => 'origin-top-left left-0',
            ['down', 'right']   => 'origin-top-right right-0',
            ['down', 'top']     => 'origin-top',
            ['up', 'left']      => 'origin-bottom-left left-0',
            ['up', 'right']     => 'origin-bottom-right right-0',
            ['up', 'top']       => 'origin-bottom',
        };

        if ($drop === 'up')
            $result .= ' bottom-full';

        return $result;
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
     * Get the margin classes for the dropdown menu.
     */
    private function marginClasses(string $drop): string
    {
        // Move the menu 1px towards its button to create a little overlap.
        return match ($drop) {
            'down' => '-mt-1',
            'up' => '-mb-1',
        };
    }

    public function render(): View
    {
        return view('components.dropdown.menu');
    }
}
