<?php

namespace Tests\Feature\Certificates;

use App\Actions\Certificates\DeleteCertificateAction;
use App\Events\Certificates\CertificateUpdatedEvent;
use App\Jobs\Certificates\DeleteLetsEncryptCertificateJob;
use App\Models\Certificate;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;
use RuntimeException;

class DeleteCertificateActionTest extends AbstractFeatureTest
{
    public function test_job_gets_dispatched()
    {
        /** @var Certificate $cert */
        $cert = Certificate::factory()
            ->withServer()
            ->inState('installed')
            ->create();

        /** @var DeleteCertificateAction $action */
        $action = $this->app->make(DeleteCertificateAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($cert);

        $this->assertDatabaseHas('certificates', [
            'id' => $cert->id,
            'state' => 'deleting',
        ]);

        Bus::assertDispatched(DeleteLetsEncryptCertificateJob::class);
        Event::assertDispatched(CertificateUpdatedEvent::class);
    }

    public function test_invalid_type_gets_handled()
    {
        /** @var Certificate $cert */
        $cert = Certificate::factory([
            'type' => 'foobar',
        ])
            ->withServer()
            ->inState('installed')
            ->create();

        /** @var DeleteCertificateAction $action */
        $action = $this->app->make(DeleteCertificateAction::class);

        Bus::fake();
        Event::fake();

        $this->expectException(RuntimeException::class);

        $action->execute($cert);

        $this->assertDatabaseHas('certificates', [
            'id' => $cert->id,
            'state' => 'installed',
        ]);

        Bus::assertNotDispatched(DeleteLetsEncryptCertificateJob::class);
        Event::assertNotDispatched(CertificateUpdatedEvent::class);
    }

    /** @dataProvider irrelevantStates */
    public function test_irrelevant_certificates_get_ignored(string $state)
    {
        /** @var Certificate $cert */
        $cert = Certificate::factory()
            ->withServer()
            ->inState($state)
            ->create();

        /** @var DeleteCertificateAction $action */
        $action = $this->app->make(DeleteCertificateAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($cert);

        $this->assertDatabaseHas('certificates', [
            'id' => $cert->id,
            'state' => $state,
        ]);

        Bus::assertNotDispatched(DeleteLetsEncryptCertificateJob::class);
        Event::assertNotDispatched(CertificateUpdatedEvent::class);
    }

    public function irrelevantStates(): array
    {
        return [
            ['deleting'],
        ];
    }
}
