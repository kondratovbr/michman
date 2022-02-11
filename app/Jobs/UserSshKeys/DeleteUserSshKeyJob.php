<?php declare(strict_types=1);

namespace App\Jobs\UserSshKeys;

use App\Actions\UserSshKeys\DeleteUserSshKeyAction;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\UserSshKey;

class DeleteUserSshKeyJob extends AbstractJob
{
    use IsInternal;

    protected UserSshKey $key;

    public function __construct(UserSshKey $key)
    {
        parent::__construct();

        $this->key = $key->withoutRelations();
    }

    public function handle(DeleteUserSshKeyAction $action): void
    {
        $action->execute($this->key);
    }
}
