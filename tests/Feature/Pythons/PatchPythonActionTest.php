<?php

namespace Tests\Feature\Pythons;

use App\Actions\Pythons\PatchPythonAction;
use App\Jobs\Pythons\PatchPythonJob;
use App\Models\Python;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class PatchPythonActionTest extends AbstractFeatureTest
{
    public function test_action()
    {
        /** @var Python $python */
        $python = Python::factory()->withServer()->create();

        /** @var PatchPythonAction $action */
        $action = $this->app->make(PatchPythonAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($python);

        $python->refresh();

        $this->assertTrue($python->isUpdating());
        $this->assertEquals(Python::STATUS_UPDATING, $python->status);

        Bus::assertDispatched(PatchPythonJob::class);
    }
}
