<?php declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Onboarding extends Component
{
    //
    public function render(): View
    {
        return view('components.onboarding.section', [
            'steps' => 6,
            'completedSteps' => 0,
        ]);
    }
}
