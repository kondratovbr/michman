<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\Deployments\DeploymentFailedEvent;
use App\Models\Deployment;
use Illuminate\Http\Request;

class TestController extends AbstractController
{
    public function __invoke(Request $request)
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::query()->latest()->firstOrFail();

        DeploymentFailedEvent::dispatch($deployment);

        return view('debug.blank');
    }
}
