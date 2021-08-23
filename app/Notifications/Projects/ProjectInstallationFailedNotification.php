<?php declare(strict_types=1);

namespace App\Notifications\Projects;

class ProjectInstallationFailedNotification extends AbstractProjectNotification
{
    protected bool $broadcast = true;
}
