<?php declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    public string $sizeClasses;

    public function __construct(string $size = '4')
    {
        $this->sizeClasses = $this->getSizeClasses($size);
    }

    /**
     * Get CSS classes for the icon container size.
     */
    private function getSizeClasses(string $size): string
    {
        return match($size) {
            '4' => 'w-4 h-4',
            '8' => 'w-8 h-8',
            '10' => 'w-10 h-10',
            '12' => 'w-12 h-12',
        };
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.icon');
    }
}
