<?php declare(strict_types=1);

namespace App\Services\LogSnag;

enum SnagEvent: string
{
    // Users
    case USER_REGISTERED = 'user-registered';

    // Servers
    case SERVER_CREATED = 'server-created';

    // Projects
    case PROJECT_CREATED = 'project-created';
    case DEPLOYMENT_FINISHED = 'deployment-finished';
}
