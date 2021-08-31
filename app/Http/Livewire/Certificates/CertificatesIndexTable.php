<?php declare(strict_types=1);

namespace App\Http\Livewire\Certificates;

use App\Broadcasting\ProjectChannel;
use App\Events\Certificates\CertificateCreatedEvent;
use App\Events\Certificates\CertificateDeletedEvent;
use App\Events\Certificates\CertificateUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Certificate;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class CertificatesIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Project $project;

    public Collection $certificates;

    /** @var string[] */
    protected $listeners = [
        'certificate-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ProjectChannel::name($this->project),
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
        $this->authorize('index', [Certificate::class, $this->project]);
    }

    public function render(): View
    {
        $this->certificates = $this->project->certificates()->oldest()->get();

        return view('certificates.certificates-index-table');
    }
}
