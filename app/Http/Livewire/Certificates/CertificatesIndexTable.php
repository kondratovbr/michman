<?php declare(strict_types=1);

namespace App\Http\Livewire\Certificates;

use App\Actions\Certificates\DeleteCertificateAction;
use App\Broadcasting\ServerChannel;
use App\Events\Certificates\CertificateCreatedEvent;
use App\Events\Certificates\CertificateDeletedEvent;
use App\Events\Certificates\CertificateUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Certificate;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests.

/*
 * TODO: CRITICAL! Don't forget to implement removal and copying,
 *       i.e. it should be possible to copy a certificate from another server.
 *       It should be automatically renewed only on the original server, of course.
 *       So make sure there IS an original server for every existing certificate at all times.
 */

class CertificatesIndexTable extends LivewireComponent
{
    use AuthorizesRequests;
    use ListensForEchoes;

    public Server $server;

    public Collection $certificates;

    /** @var string[] */
    protected $listeners = [
        'certificate-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServerChannel::name($this->server),
            [
                CertificateCreatedEvent::class,
                CertificateUpdatedEvent::class,
                CertificateDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [Certificate::class, $this->server]);
    }

    /**
     * Delete a certificate.
     */
    public function delete(string $key, DeleteCertificateAction $action): void
    {
        $cert = Certificate::validated($key, $this->certificates);

        $this->authorize('delete', $cert);

        $action->execute($cert);
    }

    public function render(): View
    {
        $this->certificates = $this->server->certificates()->oldest()->get();

        return view('certificates.certificates-index-table');
    }
}
