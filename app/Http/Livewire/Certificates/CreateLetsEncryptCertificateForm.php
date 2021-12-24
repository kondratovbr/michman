<?php declare(strict_types=1);

namespace App\Http\Livewire\Certificates;

use App\Actions\Certificates\StoreLetsEncryptCertificateAction;
use App\Models\Certificate;
use App\Models\Server;
use App\Support\Arr;
use App\Support\Str;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! I've done major changes so test the whole process again.

// TODO: CRITICAL! Implement some progress display.

/*
 * TODO: CRITICAL! Need to make sure that duplicate certificates cannot be created. If a user tries to add certificates for subdomains - certbot will just force "expand" the existing one, so we should handle it here.
 */

/*
 * TODO: IMPORTANT! Don't forget to explain somewhere in docs that the DNS should be configured by the user beforehand.
 */

// TODO: CRITICAL! Cover with tests.

class CreateLetsEncryptCertificateForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Server $server;

    public string $domains = '';

    protected function prepareForValidation($attributes): array
    {
        if (is_string($attributes['domains'])) {
            $attributes['domains'] = Arr::map(
                explode(',', Str::lower($attributes['domains'])),
                fn(string $domain) => trim($domain)
            );
        }

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'domains' => Rules::array()->nullable(),
            'domains.*' => Rules::domain(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', [Certificate::class, $this->server]);

        $this->resetState();
    }

    public function resetState(): void
    {
        $this->reset('domains');
    }

    /** Store the new certificate. */
    public function store(StoreLetsEncryptCertificateAction $action): void
    {
        $domains = $this->validate()['domains'];

        $this->authorize('create', [Certificate::class, $this->server]);

        $action->execute($this->server, $domains);

        $this->emit('certificate-stored');

        $this->resetState();
    }

    public function render(): View
    {
        return view('certificates.create-lets-encrypt-certificate-form');
    }
}
