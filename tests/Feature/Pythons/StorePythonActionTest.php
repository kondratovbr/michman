<?php

namespace Tests\Feature\Pythons;

use App\Actions\Pythons\StorePythonAction;
use App\Jobs\Pythons\InstallPythonJob;
use App\Models\Python;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StorePythonActionTest extends AbstractFeatureTest
{
    public function test_python_gets_stored()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var StorePythonAction $action */
        $action = $this->app->make(StorePythonAction::class);

        Bus::fake();
        Event::fake();

        $python = $action->execute('3_9', $server);

        $this->assertDatabaseHas('pythons', [
            'id' => $python->id,
            'version' => '3_9',
            'status' => Python::STATUS_INSTALLING,
        ]);

        Bus::assertDispatched(InstallPythonJob::class);
    }
}
