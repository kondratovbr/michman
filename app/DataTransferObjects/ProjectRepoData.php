<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ProjectRepoData extends DataTransferObject
{
    public string $repo;
    public string $branch;
    public bool $use_deploy_key;
}
