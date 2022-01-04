<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Certificate;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: IMPORTANT! Cover with tests.

class CertificatePolicy
{
    use HandlesAuthorization;

    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function create(User $user, Server $server): bool
    {
        if (! $user->is($server->user))
            return false;

        return in_array($server->type, ['app', 'web', 'balancer']);
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->is($certificate->user);
    }
}
