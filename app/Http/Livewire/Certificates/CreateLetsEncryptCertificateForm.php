<?php declare(strict_types=1);

namespace App\Http\Livewire\Certificates;

use App\Actions\Certificates\StoreLetsEncryptCertificateAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Certificate;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component as LivewireComponent;

// TODO: IMPORTANT! Don't forget to explain somewhere in docs that the DNS should be configured by the user beforehand.

// TODO: IMPORTANT! Cover with tests.

class CreateLetsEncryptCertificateForm extends LivewireComponent
{
    use AuthorizesRequests;
    use TrimsInputBeforeValidation;

    public Server $server;

    public string $domain = '';

    protected function rules(): array
    {
        return [
            'domain' => Rules::domain()
                ->addRule(Rule::unique('certificates', 'domain')->where(
                    fn(Builder $query) => $query->where('server_id', $this->server->getKey())
                ))
                ->required(),
        ];
    }

    protected function messages(): array
    {
        return [
            'domain.unique' => __('validation.custom.cert-domain-duplicate'),
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', [Certificate::class, $this->server]);

        $this->resetState();
    }

    public function resetState(): void
    {
        $this->reset('domain');
    }

    /** Store the new certificate. */
    public function store(StoreLetsEncryptCertificateAction $action): void
    {
        $domain = $this->validate()['domain'];

        $this->authorize('create', [Certificate::class, $this->server]);

        $action->execute($this->server, $domain);

        $this->emit('certificate-stored');

        $this->resetState();
    }

    public function render(): View
    {
        return view('certificates.create-lets-encrypt-certificate-form');
    }
}
