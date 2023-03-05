<?php declare(strict_types=1);

namespace App\Services\LogSnag;

enum SnagChannel: string
{
    case TEST = 'test';
    case USERS = 'users';
    case SERVERS = 'servers';
    case PROJECTS = 'projects';
}
