<?php declare(strict_types=1);

namespace App\Rules;

use App\Models\Server;
use Illuminate\Contracts\Validation\Rule;

class ProjectDomainUnique implements Rule
{
    public function __construct(
        protected Server $server,
    ) {}

    public function passes($attribute, $value): bool
    {
        if (! is_string($value))
            return false;

        return ! $this->server->projects()->where('domain', $value)->exists();
    }

    public function message(): string
    {
        return __('validation.custom.domain-unique');
    }
}
