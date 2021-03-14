<?php declare(strict_types=1);

namespace App\View\Components\Layouts;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppOneColumn extends Component
{
    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app-one-column');
    }
}
