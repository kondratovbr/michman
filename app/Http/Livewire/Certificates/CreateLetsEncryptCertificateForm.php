<?php declare(strict_types=1);

namespace App\Http\Livewire\Certificates;

use App\Actions\Certificates\StoreLetsEncryptCertificateAction;
use App\Models\Certificate;
use App\Models\Project;
use App\Support\Arr;
use App\Support\Str;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class CreateLetsEncryptCertificateForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;

    public string $domains;

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
        $this->authorize('create', [Certificate::class, $this->project]);

        $this->resetState();
    }

    protected function resetState(): void
    {
        $this->domains = "{$this->project->domain}";
    }

    /**
     * Store the new certificate.
     */
    public function store(StoreLetsEncryptCertificateAction $action): void
    {
        $domains = $this->validate()['domains'];

        $this->authorize('create', [Certificate::class, $this->project]);

        $action->execute($this->project, $domains);

        $this->emit('certificate-stored');

        $this->resetState();
    }

    public function render(): View
    {
        return view('certificates.create-lets-encrypt-certificate-form');
    }
}
