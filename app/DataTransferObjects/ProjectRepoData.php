<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ProjectRepoData extends DataTransferObject
{
    public string $root;
    public string $repo;
    public string $branch;
    public string $package;
    public bool $use_deploy_key;
    public string|null $requirements_file = null;
}
