<?php declare(strict_types=1);

namespace App\Jobs\UserSshKeys;

use App\Actions\UserSshKeys\DeleteUserSshKeyAction;
use App\Jobs\AbstractJob;
use App\Models\UserSshKey;

// TODO: CRITICAL! Cover with tests.

class DeleteUserSshKeyJob extends AbstractJob
{
    protected UserSshKey $key;

    public function __construct(UserSshKey $key)
    {
        $this->key = $key->withoutRelations();
    }

    public function handle(DeleteUserSshKeyAction $action): void
    {
        $action->execute($this->key);
    }
}
