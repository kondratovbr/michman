<?php declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalNew extends Component
{
    public function __construct(
        public string $id
    ) {}

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.modal-new');
    }
}
