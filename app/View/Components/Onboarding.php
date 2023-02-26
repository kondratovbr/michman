<?php declare(strict_types=1);

namespace App\View\Components;

use App\Support\Arr;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Onboarding extends Component
{
    public function render(): View
    {
        $user = user();

        $steps = [];

        $steps[1] = [
            'completed' => $user->vcsProviders()->exists(),
        ];

        $steps[2] = [
            'completed' => $user->providers()->exists(),
        ];

        $steps[3] = [
            'completed' => $user->servers()->exists(),
        ];

        $steps[4] = [
            'completed' => $user->projects()->exists(),
            'server_id' => $user->servers()->whereDoesntHave('projects')->first()?->id,
        ];

        $steps[5] = [
            'completed' => $user->projects()->configured()->exists(),
            'project_id' => $user->projects()->unconfigured()->first()?->id,
        ];

        $steps[6] = [
            'completed' => $user->subscribed(),
        ];

        return view('components.onboarding.section', [
            'user' => $user,
            'steps' => $steps,
            'totalSteps' => count($steps),
            'completedSteps' => count(Arr::where($steps, fn(array $step) => $step['completed'])),
        ]);
    }
}
