<?php

namespace Tests\Feature\Certificates;

use App\Actions\Certificates\StoreLetsEncryptCertificateAction;
use App\Events\Certificates\CertificateCreatedEvent;
use App\Jobs\Certificates\InstallLetsEncryptCertificateJob;
use App\Models\Certificate;
use App\Models\Server;
use App\States\Certificates\Installing;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreLetsEncryptCertificateActionTest extends AbstractFeatureTest
{
    public function test_certificate_gets_stored()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var StoreLetsEncryptCertificateAction $action */
        $action = $this->app->make(StoreLetsEncryptCertificateAction::class);

        Bus::fake();
        Event::fake();

        $cert = $action->execute($server, 'foo.com');

        $this->assertDatabaseHas('certificates', [
            'id' => $cert->id,
            'server_id' => $server->id,
            'domain' => 'foo.com',
            'type' => 'lets-encrypt',
            'state' => 'installing',
        ]);

        $server->refresh();

        /** @var Certificate $cert */
        $cert = $server->certificates()->firstOrFail();

        $this->assertTrue($cert->state->is(Installing::class));

        Bus::assertDispatched(InstallLetsEncryptCertificateJob::class);
        Event::assertDispatched(CertificateCreatedEvent::class);
    }
}
