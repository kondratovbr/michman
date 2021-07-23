<?php declare(strict_types=1);

namespace App\Events\VcsProviders;

use App\Events\Users\AbstractUserEvent;
use App\Models\VcsProvider;

abstract class AbstractVcsProviderEvent extends AbstractUserEvent
{
    public int $vcsProviderKey;

    public function __construct(VcsProvider $vcsProvider)
    {
        parent::__construct($vcsProvider->user);

        $this->vcsProviderKey = $vcsProvider->getKey();
    }
}
