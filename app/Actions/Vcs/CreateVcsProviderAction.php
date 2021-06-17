<?php declare(strict_types=1);

namespace App\Actions\Vcs;

use App\DataTransferObjects\VcsProviderData;
use App\Models\User;
use App\Models\VcsProvider;

class CreateVcsProviderAction
{
    public function execute(VcsProviderData $data, User $user): VcsProvider
    {
        //
    }
}
